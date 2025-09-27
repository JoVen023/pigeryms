<?php
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
		
header('location:index.php');
}
else{
    
  
    if(isset($_GET['id'])) {
        $breederId = intval($_GET['id']);
    } else {
      
        die('ID not provided.');
    }

// Retrieve the pig details from the database using the $pigId
$query = "SELECT * FROM tblpigbreeders WHERE id = :pigId";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $breederId, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pig){
    echo "<h2>Pig no Found</h2>";
    return;
}
$pigname = $pig['name'];
$total_farrow= $pig['total_farrowed'];

$styleAttribute=($total_farrow !=0)?'' : 'display:none';

$date = !empty($pig['date']) ? new DateTime($pig['date']) : null;
$formatteddate = $date ? $date->format('F j, Y') : null;
$breedingdate = !empty($pig['breedingstart']) ? new DateTime($pig['breedingstart']) : null;
$formattedbreed = $breedingdate ? $breedingdate->format('F j, Y')  : null;
$forrowingdate = !empty($pig['forrowingdate']) ? new DateTime($pig['forrowingdate']) : null;
$formattedforrow = $forrowingdate ? $forrowingdate->format('F j, Y') : null;


$breedValue = $pig['breedingstart'];
    $isValid = (DateTime::createFromFormat('Y-m-d', $breedValue) !== false) && ($breedValue !== '0000-00-00');

    
$forrowValue = $pig['forrowingdate'];
$isVal = (DateTime::createFromFormat('Y-m-d', $forrowValue) !== false) && ($forrowValue !== '0000-00-00');

if($forrowingdate) {
    $fifteenDayAfter = clone $forrowingdate;  // Clone the original DateTime object to avoid modifying it
    $fifteenDayAfter->add(new DateInterval('P15D')); // Add 1 day
    $formattedfifteenDayAfter = $fifteenDayAfter->format('F j, Y');

    $oneDayAfter = clone $forrowingdate;  // Clone the original DateTime object to avoid modifying it
    $oneDayAfter->add(new DateInterval('P1D')); // Add 1 day
    $formattedOneDayAfter = $oneDayAfter->format('F j, Y');
  
    $twoDayAfter = clone $forrowingdate;  // Clone the original DateTime object to avoid modifying it
    $twoDayAfter->add(new DateInterval('P2D')); // Add 1 day
    $formattedtwoDayAfter = $twoDayAfter->format('F j, Y');

    $twentyDayAfter = clone $forrowingdate;  // Clone the original DateTime object to avoid modifying it
    $twentyDayAfter->add(new DateInterval('P20D')); // Add 1 day
    $formattedtwentyDayAfter = $twentyDayAfter->format('F j, Y');
}

$dateValue = $pig['gestateends'];
    $isValidDate = (DateTime::createFromFormat('Y-m-d', $dateValue) !== false) && ($dateValue !== '0000-00-00');

$gestatedate = !empty($pig['gestateends']) ? new DateTime($pig['gestateends']) : null;
$formattedgestate = $gestatedate ?  $gestatedate->format('F j, Y') : null;

$weaningDate = new DateTime($pig['gestateends']);
$currentDate = new DateTime();  // Current date

// Remove the time portion for accurate comparison
$weaningDate->setTime(0, 0, 0);
$currentDate->setTime(0, 0, 0);


if(isset($_POST['record'])){
    $id = $breederId;
    $farrowed = $_POST['farrowed'];
    $total = $_POST['total'];
    $weaned = $_POST['weaned'];
    $survived = $_POST['survived'];
    
    // Prepare the query
    $query = $dbh->prepare("INSERT INTO breeder_records (breeder_id, date_farrowed, weaned_date, total_piglets, survived) VALUES (:breeder_id,  :date_farrowed, :weaned_date, :total_piglets, :survived)");

    // Bind the parameters
    $query->bindParam(':breeder_id', $id, PDO::PARAM_STR);

    $query->bindParam(':date_farrowed', $farrowed, PDO::PARAM_STR);
    $query->bindParam(':weaned_date', $weaned, PDO::PARAM_STR);
    $query->bindParam(':total_piglets', $total, PDO::PARAM_STR);
    $query->bindParam(':survived', $survived, PDO::PARAM_STR);

    try {
        $query->execute();
        if ($query) {
            $success = "Added" && header("refresh:1; url=breederdetails.php?id=" . $id);
        } else {
            $err = "Please Try Again Or Try Later";
        }
    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    }
}



if(isset($_POST['updaterecord'])){
    $id = $breederId;
    $Id = intval($_POST['id']);  // Convert ID to integer
    $farrow = $_POST['farrowed'];
    $wean = $_POST['weaned'];
    $survived = $_POST['survive'];
    $total = $_POST['total'];

    $query= $dbh->prepare("UPDATE breeder_records SET date_farrowed=:farrow, weaned_date=:weaned,total_piglets=:total,survived=:survived WHERE id=:id");
     $query->bindParam(':farrow',$farrow,PDO::PARAM_STR);
     $query->bindParam('weaned',$wean,PDO::PARAM_STR);
     $query->bindParam('id',$Id,PDO::PARAM_INT);
     $query->bindParam('total',$total,PDO::PARAM_INT);
     $query->bindParam('survived',$survived,PDO::PARAM_INT);
     try {
        $query->execute();
        if ($query) {
            $success = "Product Added" && header("refresh:1; url=breederdetails.php?id=" . $id);
        } else {
            $err = "Please Try Again Or Try Later";
        }
    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    }
}




if(isset($_POST['update'])){
    $Id = intval($_POST['id']);  // Convert ID to integer
    $pigname = $_POST['name'];
    $age = $_POST['age'];
    $pigsm = $_POST['male'];
    $farrowed = $_POST['farrowed'];
    $totalp = $_POST['totalpiglets'];
    $pigsf = $_POST['female'];
    $forrowingdate = $_POST['forrowingdate'];
    $breedingdate = $_POST['breedingdate'];
    $status = $_POST['status'];

    if ($totalp != NULL && $totalp != 0 ){
        $pigs=$totalp;
    }
    else{
    $pigs=$pigsm+$pigsf;
}
   
    // Initially set the filename as null
    $filename = null;

    // Fetch current data from database
    $fetchQuery = $dbh->prepare("SELECT * FROM tblpigbreeders WHERE id = :id");
    $fetchQuery->bindParam(':id', $Id, PDO::PARAM_STR);
    $fetchQuery->execute();
    $currentData = $fetchQuery->fetch(PDO::FETCH_OBJ);

    // If an image was uploaded, update the filename
    if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) { 
        $filename = basename($_FILES['pict']['name']);
        $uploadPath = 'img/' . $filename;
        
        // Move the uploaded file to the desired directory
        if (!move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {
            $filename = null;
        }
    }

    // If no new image was uploaded, keep the existing image
    if (!$filename) {
        $filename = $currentData->img;
    }

    


    if ($status == "Farrowing") {
      
        $breedingdateTime = new DateTime($breedingdate);
        $breedingdateTime->add(new DateInterval('P100D'));
        $newDates = $breedingdateTime->format('Y-m-d');
        $query = $dbh->prepare("UPDATE tblpigbreeders SET name=:name, age=:age, status=:status, img=:pict, breedingstart=:breedingdate, forrowingdate=:forrowingdate WHERE id=:id");
        $query->bindParam(':name', $pigname, PDO::PARAM_STR);
        $query->bindParam(':age', $age, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
       
        $query->bindParam(':breedingdate', $breedingdate, PDO::PARAM_STR);
        $query->bindParam(':forrowingdate', $newDates, PDO::PARAM_STR);
        $query->bindParam(':pict', $filename, PDO::PARAM_STR);
        $query->bindParam(':id', $Id, PDO::PARAM_INT);

    }
    // && $pigsm != NULL && $pigsf != 0 && $pigsm != 0 && $pigsf != NULL
    elseif ($status == "Lactating") {
        $forrowingDateTime = new DateTime($forrowingdate);                     
        $forrowingDateTime->add(new DateInterval('P40D'));
        $newDate = $forrowingDateTime->format('Y-m-d');
        $forrowingguides = new DateTime($forrowingdate);
        $forrowingguides->add(new DateInterval('P1D'));
        $vitamins = $forrowingguides->format('Y-m-d');
        $forrowingguides->add(new DateInterval('P1D'));
        $iron = $forrowingguides->format('Y-m-d');
        $forrowingguides->add(new DateInterval('P18D'));
        $kapon = $forrowingguides->format('Y-m-d');
        
        $query = $dbh->prepare("UPDATE tblpigbreeders SET name=:name, age=:age, status=:status, img=:pict, forrowingdate=:forrowingdate,piglets=:pigs, male=:pigsm, female=:pigsf, gestateends=:gestateend WHERE id=:id");

        $query->bindParam(':name', $pigname, PDO::PARAM_STR);
        $query->bindParam(':age', $age, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':forrowingdate', $forrowingdate, PDO::PARAM_STR);
        $query->bindParam(':pigs', $pigs, PDO::PARAM_INT);
        $query->bindParam(':pigsm', $pigsm, PDO::PARAM_INT);
        $query->bindParam(':pigsf', $pigsf, PDO::PARAM_INT);
        $query->bindParam(':gestateend', $newDate, PDO::PARAM_STR);
        $query->bindParam(':pict', $filename, PDO::PARAM_STR);
        $query->bindParam(':id', $Id, PDO::PARAM_INT);
    }

    elseif ( $totalp != NULL && $totalp != 0 ) {
        $total=$farrowed+1;
         $survived=0;
         $wean=0000-00-00;
         $status="Breeding";
         $breeder=$breederId;
        $query2 = $dbh->prepare("INSERT INTO  breeder_records (breeder_id,date_farrowed,weaned_date,total_piglets,survived) VALUES (:breeder_id,:date_farrowed,:weaned_date,:total_piglets,:survived)");

        $query2->bindParam(':breeder_id', $breeder, PDO::PARAM_STR);
        $query2->bindParam(':date_farrowed', $forrowingdate, PDO::PARAM_STR);
        $query2->bindParam(':weaned_date', $wean, PDO::PARAM_STR);
        $query2->bindParam(':total_piglets', $pigs, PDO::PARAM_STR);
        $query2->bindParam(':survived', $survived, PDO::PARAM_INT);
        $query2->execute();


        $query = $dbh->prepare("UPDATE tblpigbreeders SET name=:name, age=:age, status=:status,total_farrowed=:total, img=:pict, breedingstart=0000-00-00, forrowingdate=0000-00-00, gestateends=0000-00-00, piglets=NULL WHERE id=:id");

        $query->bindParam(':name', $pigname, PDO::PARAM_STR);
        $query->bindParam(':age', $age, PDO::PARAM_STR);
        $query->bindParam(':total', $total, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':pict', $filename, PDO::PARAM_STR);
        $query->bindParam(':id', $Id, PDO::PARAM_INT);


        $deleteTodoQuery = $dbh->prepare("DELETE FROM tbltodo WHERE sow_id=:id");
        $deleteTodoQuery->bindParam(':id', $breeder, PDO::PARAM_INT);
        $deleteTodoQuery->execute();

        
    }
     
   
    else {
        $query = $dbh->prepare("UPDATE tblpigbreeders SET name=:name, age=:age, status=:status, img=:pict, breedingstart=0000-00-00, forrowingdate=0000-00-00, gestateends=0000-00-00, piglets=NULL WHERE id=:id");

        $query->bindParam(':name', $pigname, PDO::PARAM_STR);
        $query->bindParam(':age', $age, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':pict', $filename, PDO::PARAM_STR);
        $query->bindParam(':id', $Id, PDO::PARAM_INT);

        $deleteTodoQuery = $dbh->prepare("DELETE FROM tbltodo WHERE sow_id=:id");
        $deleteTodoQuery->bindParam(':id', $Id, PDO::PARAM_INT);
        $deleteTodoQuery->execute();

       
    }

    $fetchTodoQuery = $dbh->prepare("SELECT details, time FROM tbltodo WHERE sow_id = :id");
    $fetchTodoQuery->bindParam(':id', $Id, PDO::PARAM_INT);
    $fetchTodoQuery->execute();
    $existingTodos = $fetchTodoQuery->fetchAll(PDO::FETCH_ASSOC);
    
    $existingDetails = array_column($existingTodos, 'details');
    
    if ($status == "Farrowing") {
        $todoTextForrowing = 'Farrowing';
        if (in_array($todoTextForrowing, $existingDetails)) {
            // Update if exists
            $currentDateKey = array_search($todoTextForrowing, $existingDetails);
            if ($existingTodos[$currentDateKey]['time'] !== $newDates) {
                $updateTodoQuery = $dbh->prepare("UPDATE tbltodo SET time=:newDates WHERE sow_id=:id AND details=:details");
                $updateTodoQuery->bindParam(':newDates', $newDates, PDO::PARAM_STR);
                $updateTodoQuery->bindParam(':id', $Id, PDO::PARAM_INT);
                $updateTodoQuery->bindParam(':details', $todoTextForrowing, PDO::PARAM_STR);
                $updateTodoQuery->execute();
            }
        } else {
            // Insert if it doesn't exist
            $insertQuery = $dbh->prepare("INSERT INTO tbltodo (sow_id, details, time) VALUES (:id, :text, :forrowingdate)");
            $insertQuery->bindParam(':forrowingdate', $newDates, PDO::PARAM_STR);
            $insertQuery->bindParam(':text', $todoTextForrowing, PDO::PARAM_STR);
            $insertQuery->bindParam(':id', $Id, PDO::PARAM_INT);
            $insertQuery->execute();
        }

    } elseif ($status == "Lactating") {
        // Define the TODOs
        $todos = [
            'Weaning' => $newDate,
            'Vitamins' => $vitamins,
            'Injecting Iron' => $iron,
            'Kapon' => $kapon
        ];
    
        foreach ($todos as $todoText => $todoDate) {
            if (in_array($todoText, $existingDetails)) {
                // Update if exists
                $currentDateKey = array_search($todoText, $existingDetails);
                if ($existingTodos[$currentDateKey]['time'] !== $todoDate) {
                    $updateTodoQuery = $dbh->prepare("UPDATE tbltodo SET time=:todoDate WHERE sow_id=:id AND details=:details");
                    $updateTodoQuery->bindParam(':todoDate', $todoDate, PDO::PARAM_STR);
                    $updateTodoQuery->bindParam(':id', $Id, PDO::PARAM_INT);
                    $updateTodoQuery->bindParam(':details', $todoText, PDO::PARAM_STR);
                    $updateTodoQuery->execute();
                }
            } else {
                // Insert if it doesn't exist
                $insertQuery = $dbh->prepare("INSERT INTO tbltodo (sow_id, details, time) VALUES (:id, :text, :date)");
                $insertQuery->bindParam(':date', $todoDate, PDO::PARAM_STR);
                $insertQuery->bindParam(':text', $todoText, PDO::PARAM_STR);
                $insertQuery->bindParam(':id', $Id, PDO::PARAM_INT);
                $insertQuery->execute();
            }
        }
    }
    // Execute the query
    try {
        $query->execute();
        if ($query) {
            $success = "Added" && header("refresh:1; url=breederdetails.php?id=" . $Id);
        } else {
            $err = "Please Try Again Or Try Later";
        }

        // echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'breederdetails.php?id=" . $Id . "';</script>";

    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    } 

}


if(isset($_POST['move'])){
    $id = intval($_POST['sow_id']);  // Convert ID to integer
    $pigname = $_POST['name'];
    $pig = $_POST['pig'];
    $total_farrowed = $_POST['total_farrowed'];
    $male = $_POST['male'];
    $female = $_POST['female'];
    $totalpigs = $male +$female;
    $weaned = $_POST['movingdate'];
    $date_farrowed = $_POST['date_farrowed'];
    $weaner = new DateTime($weaned);
    $wean = clone $weaner;  // Clone the original DateTime object to avoid modifying it
    $wean->add(new DateInterval('P31D')); // Add 32 day
    $thirtyoneDayAfter = $wean->format('Y-m-d');
    $wean->add(new DateInterval('P20D')); // Add 20 day
    $fiftyoneDayAfter = $wean->format('Y-m-d');
    $wean->add(new DateInterval('P30D')); // Add 30 day
    $eightyoneDayAfter = $wean->format('Y-m-d');
    $wean->add(new DateInterval('P50D')); // Add 50 day
    $growerDayAfter = $wean->format('Y-m-d');
    $wean->add(new DateInterval('P15D')); // Add 50 day
    $finisherDayAfter = $wean->format('Y-m-d');
    $total=$total_farrowed+1;
    $formattedCurrentDate = $currentDate->format('Y-m-d');

if ($formattedCurrentDate >= $finisherDayAfter) {
    $stats = "Completed";
} elseif ($formattedCurrentDate >= $growerDayAfter) {
    $stats = "Finisher";
} elseif ($formattedCurrentDate >= $eightyoneDayAfter) {
    $stats = "Grower";  
} elseif ($formattedCurrentDate >= $fiftyoneDayAfter) {
    $stats = "Starter";
} elseif ($formattedCurrentDate >= $thirtyoneDayAfter) {
    $stats = "Pre-Starter";
} else {
    // If none of the above conditions are met, set a default status or don't update
    $stats = "PiggyBloom"; // replace 'DefaultStatus' with whatever default status you want or simply don't set the $stats variable
}

$query1 = $dbh->prepare("INSERT INTO breeder_records(breeder_id, date_farrowed, weaned_date, total_piglets, survived) VALUES(:id, :date_farrowed, :weaned, :total_pigs, :survived)");

// Bind all parameters
$query1->bindParam(':id', $id, PDO::PARAM_INT);
$query1->bindParam(':date_farrowed', $date_farrowed, PDO::PARAM_STR);
$query1->bindParam(':weaned', $weaned, PDO::PARAM_STR); // Add the missing parameter
$query1->bindParam(':total_pigs', $pig, PDO::PARAM_INT); // Corrected parameter name
$query1->bindParam(':survived', $totalpigs, PDO::PARAM_INT);
$query1->execute();


    $filename = null;

    try {
     
    // Fetch current data from database
    $fetchQuery = $dbh->prepare("SELECT * FROM tblpigbreeders WHERE id = :id");
    $fetchQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $fetchQuery->execute();
    $currentData = $fetchQuery->fetch(PDO::FETCH_OBJ);

    // If an image was uploaded, update the filename
    if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) { 
        $filename = basename($_FILES['pict']['name']);
        $uploadPath = 'img/' . $filename;
        
        // Move the uploaded file to the desired directory
        if (!move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {
            $filename = null;
        }
    }
    // If no new image was uploaded, keep the existing image
    if (!$filename) {
        $filename = $currentData->img;
    }

    $query = $dbh->prepare("INSERT INTO tblgrowingphase(sow_id, sowname, pigs,male,female, weaneddate, img, status,piggybloom, prestarter, starter, grower, finisher)VALUES(:id, :name, :pigs,:male,:female, :weaned, :pict, :status,:piggybloom_date, :prestarter_date, :starter_date, :grower_date, :finisher_date)");

// Bind all parameters
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->bindParam(':name', $pigname, PDO::PARAM_STR);
$query->bindParam(':pigs', $totalpigs, PDO::PARAM_INT);
$query->bindParam(':male', $male, PDO::PARAM_INT);
$query->bindParam(':female', $female, PDO::PARAM_INT);
$query->bindParam(':weaned', $weaned, PDO::PARAM_STR);
$query->bindParam(':status', $stats, PDO::PARAM_STR);
$query->bindParam(':pict', $filename, PDO::PARAM_STR);
$query->bindParam(':piggybloom_date', $thirtyoneDayAfter, PDO::PARAM_STR);
$query->bindParam(':prestarter_date', $fiftyoneDayAfter, PDO::PARAM_STR);
$query->bindParam(':starter_date', $eightyoneDayAfter, PDO::PARAM_STR);
$query->bindParam(':grower_date', $growerDayAfter, PDO::PARAM_STR);
$query->bindParam(':finisher_date', $finisherDayAfter, PDO::PARAM_STR);

$query->execute();


        $sqlUpdatebreeder = "UPDATE tblpigbreeders SET status = 'Breeding', total_farrowed=:total, breedingstart = NULL, forrowingdate = NULL , gestateends = NULL, piglets = NULL WHERE id = :id;";
        $stmtUpdatebreeder = $dbh->prepare($sqlUpdatebreeder);
        $stmtUpdatebreeder->bindParam(':total', $total, PDO::PARAM_INT);
        $stmtUpdatebreeder->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtUpdatebreeder->execute();

        $sqlsDelete = "DELETE FROM tbltodo WHERE sow_id = :sowId";
        $stmtsDelete = $dbh->prepare($sqlsDelete);
        $stmtsDelete->bindParam(':sowId', $id, PDO::PARAM_INT);
        $stmtsDelete->execute();



   
    // Execute the query
    echo "<script>
    alert('Moved successfully!');
    window.location.href = 'piggrowingphase.php?msg=success';
  </script>";
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    header("Location: piggrowingphase.php?msg=error");
    exit;
    } 


    if (isset($_POST['recordid'])) {
        $recordId = $_POST['recordid']; // Corrected parameter name
        // Prepare the delete statement
        $sqlDeleterecord = "DELETE FROM breeder_records WHERE id = :recordId";
        $stmtDeleterecord = $dbh->prepare($sqlDeleterecord);
        // Bind the parameters
        $stmtDeleterecord->bindParam(':recordId', $recordId, PDO::PARAM_INT); // Corrected parameter name
        // Execute the statement
        $stmtDeleterecord->execute();
        echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'breederdetails.php?id=" . $Id . "';</script>";

    }
    
}





	
	?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Pig Breeders</title>
	<!-- CSS -->
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/x-icon" href="img/logos.jpeg">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">

<!-- SCRIPTS -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Then load Bootstrap and its dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS should be loaded after jQuery -->
<script src="js/swal.js"></script>

</head>
<body class="<?= $_SESSION['dark_mode'] ? 'dark' : '' ?>">


	<!-- SIDEBAR -->
	<?php include('includes/sidebar.php');?>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<?php include('includes/header.php');?>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>

        <div class="head-title">
				<div class="left">
					<h1>Sow Details</h1>
				
				</div>
			</div>

           
            <div class="care-timeline" style="<?php echo ($pig['status'] == "Lactating") ? 'display: block;' : 'display: none;'; ?>">
               <div class="guide">
            <h5>Lactating Guide</h5>
            <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#addModal" class="openModalBtn" 
            <?php if($currentDate < $weaningDate): echo 'style="display:none"'; endif; ?>>
    <i class='bx bx-up-arrow-circle'></i>Move Piglets
</button>

 <!-- move pig modal -->

 <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Move Piglets</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form action="<?=$_SERVER['REQUEST_URI']?>" id="formmoved" method="POST" enctype="multipart/form-data">
      <div class="row">
      <input type="hidden" name="sow_id" class="form-control" value="<?php echo $pig['id']; ?>">
      <input type="hidden" name="total_farrowed" class="form-control" value="<?php echo $pig['total_farrowed']; ?>">
      <input type="hidden" name="date_farrowed" id="forrowedate" class="form-control"  value="<?php echo $pig['forrowingdate']; ?>" autocomplete="given"/>
  <div class="col">
  <label for="sowname">Sow Name</label>
    <input type="text" name="name" id="sowname" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['name']; ?> Piglets" autocomplete="given-name" />
  </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="weandate">Weaning Date</label>
          <input type="date" id="weandate" name="movingdate" class="form-control" placeholder="Month" aria-label="Month" value="<?php echo $isValidDate ? $dateValue : ''; ?>">
        </div>
</div>
<br>
<div class="row">
<p for="pigs" class="me-1">Piglets Survived</p>
</div>
<div class="row align-items-center">
        <div class="col-6">
        <label for="male" class="me-1">Male:</label>
            <input type="number" id="male" name="male" required min="0" max="<?= $pig['male']; ?>" value="<?= $pig['male']; ?>">
            <input type="hidden" id="piglets" name="pig" value="<?php echo $pig['piglets']; ?>" >
        </div>
        <div class="col-6">
        <label for="female" class="me-1">Female:</label>
        <input type="number" id="female" name="female" required min="0" max="<?= $pig['female']?>" value="<?= $pig['female']; ?>">
        </div>
        <div id="piglet-error" class="text-danger mt-1 d-none">
            Value cannot be greater than total piglets (<?php echo $pig['piglets']; ?>)
        </div>
</div>
<br>
      <div class="row">
      <div class="col">
                                 <label for="map">Picture</label>
  									<input type="file" id="map" name="pict" class="form-control form-control-sm rounded-0">
								</div>
</div>

      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="move" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>    
<!-- movepig modal -->


</div>

                <br>
    <div class="care-tasks">
        <div class="care-task" data-day="5">Vitamins <button type="button" class="plus" data-bs-toggle="popover"  data-bs-title="Vitamins for 20 days" data-bs-content="The vitamins must be TIKI-TIKI PLUS SYRUP. 1ml/piglet for daily consumption"><i class='bx bx-comment-add'></i></button><br>(<?php echo htmlentities($formattedOneDayAfter)?>)<br><i class='bx bxs-chevrons-down'></i></div>
        <div class="care-task" data-day="15">Injecting Iron<button type="button" class="plus" data-bs-toggle="popover"  data-bs-title="Iron for 2-6 days" data-bs-content="And here's some amazing content. It's very engaging. Right?"><i class='bx bx-comment-add'></i></button><br>(<?php echo htmlentities($formattedtwoDayAfter)?>)<br><i class='bx bxs-chevrons-down'></i></div>
        <div class="care-task" data-day="25">Kapon and Vitamins<button type="button" class="plus" data-bs-toggle="popover"  data-bs-title="Castration(Kapon) and Vitamins" data-bs-content="Castration for male piglets. All piglets must be injected by vitamins()"><i class='bx bx-comment-add'></i></button><br>(<?php echo htmlentities($formattedtwentyDayAfter)?>)<br><i class='bx bxs-chevrons-down'></i></div>
        <div class="care-task" data-day="40">Weaning Day<button type="button" class="plus" data-bs-toggle="popover"  data-bs-title="Weaning Day" data-bs-content="Piglets are ready to be weaned."><i class='bx bx-comment-add'></i></button><br>(<?php echo htmlentities($formattedgestate) ?>)<br><i class='bx bxs-chevrons-down'></i></div>
    </div>
    <input type="range" min="0" max="40" value="0" id="pigCareSlider" disabled>
    
</div>




<div class="table-data bred">
            <div class="card mb-3">
  <div class="row g-0">
    <div class="col-md-4" >
    <div class="image-container">
    <img src="img/<?php echo $pig['img']; ?>" class="img-fluid rounded-start" alt="pig">
            <div class="image-overlay"></div> 
        </div>
    </div>
    <div class="col-md-8">
      <div class="card-body">
      <div class="pigsts">
    <div class="left-section"> 
<h2 class="card-title"><?php echo $pig['name']; ?></h2>
   
</div>
    <div class="right-section"> 
    <p class="card-text <?php echo $pig['status']; ?>"> <?php echo $pig['status']; ?></p>
    <button type="button" class="btn btn-sm deleteModalBtn" title="Delete Pig" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $pig['id']; ?>" data-pigid="<?php echo $pig['id']; ?>" <?php if($currentDate >= $weaningDate && $pig['status'] == 'Lactating' ): echo 'disabled'; endif; ?>><i class='bx bx-trash'></i></button><span></span>
    </div>
</div>
            <p class="card-text"><span>Age:</span> <?php echo $pig['age']; ?></p>
            <p class="card-text"><span>Number of Times Farrowed:</span> <?php echo $pig['total_farrowed']; ?></p>
           

        	<?php 
    if ($pig['status']== "Farrowing") {
        echo '<p class="card-text"><span>Breeding Start:</span> ' . htmlentities($formattedbreed) . '</p>';
        echo '<p class="card-text"><span>Farrowing Date:</span> ' . htmlentities($formattedforrow) . ' - ' . htmlentities($formattedfifteenDayAfter) . '</p>';
    } if ($pig['status'] == "Lactating") {
        echo '<p class="card-text"><span>Piglets:</span> ' . htmlentities($pig['piglets']) . '</p>';
        echo '<p class="card-text"><span>Male:</span> ' . htmlentities($pig['male']) . '</p>';
        echo '<p class="card-text"><span>Female:</span> ' . htmlentities($pig['female']) . '</p>';
		echo '<p class="card-text"><span>Farrowing Date:</span> ' . htmlentities($formattedforrow) . '</p>';
        echo '<p class="card-text"><span>Gestate Ends:</span>  ' . htmlentities($formattedgestate) . '</p>';
    }
    else{
        echo  '<br>';
    }
    
?>
<div class="button-section d-flex justify-content-center">
  <button type="button" 
          class="btn btn-md btn-primary me-2" 
          title="Update Pig" 
          data-bs-toggle="modal" 
          data-bs-target="#confirmModal" 
          data-pigid="<?php echo $pig['id']; ?>" 
          <?php if($currentDate >= $weaningDate && $pig['status'] == 'Lactating'): echo 'disabled'; endif; ?>>
    Update
  </button>
  
  <button type="button" 
          class="btn btn-md btn-danger me-2 <?= ($pig['status'] == 'Breeding') ? '' :'d-none';?>" 
          title="Cull Sow" 
          data-bs-toggle="modal" 
          data-bs-target="#cullingModal-<?= htmlentities($pig['id']) ?>" 
          data-pigid="<?= htmlentities($pig['id']) ?>">
    Move To Cull
  </button>
</div>


<!-- move to culling  Modal -->
<div class="modal fade" id="cullingModal-<?php echo $pig['id']; ?>" tabindex="-1"  aria-labelledby="cullingModalLabel-<?php echo $pig['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/culling.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to move this sow to culling?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="cullingpig('<?php echo $pig['id']; ?>')" name="culling">Confirm</button>
                </div>
            </div>
        </div>
    </div>

<!-- move to culling Modal -->

<!-- deletepig  Modal -->
<div class="modal fade" id="deleteModal-<?php echo $pig['id']; ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo $pig['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/deletepig.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this sow?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deletepig('<?php echo $pig['id']; ?>')" name="delete">Confirm</button>
                </div>
            </div>
        </div>
    </div>

<!-- delete pig Modal -->
</div>

    </div>
    <!-- update pig Modal -->

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Update Sow</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form id="myForm" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
      <input type="hidden" name="id" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['id']; ?>"/>
  <div class="col">
  <label for="fullname">Name</label>
    <input type="text"  id="fullname"name="name" class="form-control" autocomplete="given-name" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['name']; ?>"/>
  </div>
  <div class="col">
  <label for="farrowed"># Farrowed</label>
    <input type="number" id="farrowed" name="farrowed" class="form-control" placeholder="How many times Farrowed" aria-label="Farrowed" autocomplete="Farrowed" value="<?php echo $pig['total_farrowed']; ?>"readonly/>
  </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="age">Age(Month)</label>
          <input type="text" name="age" id="age"class="form-control" placeholder="Month" aria-label="Month" value="<?php echo $pig['age']; ?>"/>
        </div>
        <div class="col">
  <label for="statusSelect">Status</label>

  <select name="status" id="statusSelect" class="form-select form-select-sm"
          <?php echo ($pig['status'] == 'Lactating') ? 'disabled' : ''; ?>>
    <option value="<?php echo $pig['status']; ?>" selected><?php echo $pig['status']; ?></option>
    <?php
      $statuses = ['Breeding', 'Farrowing', 'Lactating'];
      foreach ($statuses as $status) {
        if ($status !== $pig['status']) {
          echo "<option value=\"$status\">$status</option>";
        }
      }
    ?>
  </select>

  <?php if ($pig['status'] == 'Lactating'): ?>
    <input type="hidden" name="status" value="Lactating">
  <?php endif; ?>
</div>



</div>
<br>
        
<div class="row">
    <!-- Fields for Forrowing -->
    <div class="col" id="forrowingFields" style="<?php echo ($pig['status'] == "Farrowing") ? 'display: block;' : 'display: none;'; ?>">
        <label for="breedingDate" class="me-1">Breeding Date:</label>
        <input type="date" name="breedingdate" id="breedingDate"  value="<?php echo $isValid ? $breedValue : ''; ?>"/>
    </div>

    <!-- Fields for Lactating -->
    <div class="col" id="gestatingFields" style="<?php echo ($pig['status'] == "Lactating") ? 'display: block;' : 'display: none;'; ?>">
        <label for="forrowingDate" class="me-1">Farrowing Date:</label>
        <input type="date" name="forrowingdate" id="forrowingDate"  value="<?php echo $forrowValue ?>">
   </div>
    <br>
    <div class="col" id="gestatingFieldsPigletsmale" style="<?php echo ($pig['status'] == "Lactating") ? 'display: block;' : 'display: none;'; ?>">
        <label for="pigletsm" class="me-1">Male:</label> 
        <input type="number" name="male" id="pigletsm" class="me-1" value="<?php echo $pig['male'] ?>" min="0">
    </div>
    <br>
    <div class="col" id="gestatingFieldsPigletsfemale" style="<?php echo ($pig['status'] == "Lactating") ? 'display: block;' : 'display: none;'; ?>">
        <label for="pigletsf" class="me-1">Female:</label>
        <input type="number" name="female" id="pigletsf" class="me-1" value="<?php echo $pig['female'] ?>" min="0">
    </div>
    <br>
    <div class="col mt-1 p-2" id="gestatingFieldsPigletstotal" style="<?php echo ($pig['status'] == "Lactating") ? 'display: block;' : 'display: none;'; ?>">
        <label for="totalpiglets" class="me-1">Mortality:(If all the piglets didn't survived input all the total piglets)</label>
        <input type="number" name="totalpiglets" id="totalpiglets"  class="w-50  me-1" value="<?php echo $pig['piglets'] ?>">
    </div>
    
    </div>
   


<br>
    
      <div class="row">
      <div class="col">
                                 <label for="map">Picture</label></label>
  									<input type="file" id="map" name="pict" class="form-control form-control-sm rounded-0">
								</div>
</div>

      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>    
<!-- update pig Modal -->
  </div>
</div>
				

</div>


<section class="records" style="<?php echo htmlentities($styleAttribute);?>">
<div class="head-title">
				<div class="left">
					<h1>Breeder Records</h1>
				
				</div>
                
			</div>
        <div class="table-data">
				<div class="order">
                <?php 
                          $sql = "SELECT breeder_records.*, tblpigbreeders.name AS pig_name
                          FROM breeder_records
                          JOIN tblpigbreeders ON breeder_records.breeder_id = tblpigbreeders.id
                          WHERE breeder_records.breeder_id = :breeder_id";
                  
                  $query3 = $dbh->prepare($sql);
                  $query3->bindParam(':breeder_id', $breederId, PDO::PARAM_INT);
                  $query3->execute();
                  $results = $query3->fetchAll(PDO::FETCH_OBJ);
                  $totalFarrowed = 0;
                  $totalRows = count($results);
                $disableAddButton = $totalRows >= $total_farrow;
                  ?>
				<div class="left">
					<h1>Records Lists</h1>
                    <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmModals" class="openModalBtn" <?php echo ($disableAddButton ? 'disabled' : ''); ?>>
  <i class='bx bx-plus-circle'></i> Add New
</button>
				</div>
                <table id="myTable">
						<thead>
							<tr>
                                <th>ID</th>
								<th>Name</th>
                                <th>Date Farrowed</th>
								<th>Weaned Date</th>
                                <th>Total Piglets</th>
                                <th>Survived Piglets</th>
                                <th>Action</th>
                                
							</tr>
						</thead>
                        
						<tbody>
                   
                     
                  <?php 
                          foreach($results as $result){
                            $date = new DateTime($result->date_farrowed);
                            $formatteddates = $date->format('F j, Y');
                        
                            // Check if weaned_date is not '0000-00-00' before formatting
                            if ($result->weaned_date != '0000-00-00') {
                                $dates = new DateTime($result->weaned_date);
                                $formatteddate = $dates->format('F j, Y');
                            } else {
                                $formatteddate = 'Not Weaned'; // or any other placeholder/text you want
                            }
                          
                          ?>
                              
                              <tr>
	<td>
	<p><?php echo htmlentities($result->id); ?></p>
		</td>

	<td><?php echo htmlentities($result->pig_name); ?></td>
	<td><?php echo htmlentities($formatteddates); ?></td>
    <td><?php echo htmlentities($formatteddate); ?></td>
    <td><?php echo htmlentities($result->total_piglets);?></td>
    <td><?php echo htmlentities($result->survived); ?></td>
 
    <!-- Button trigger modal -->
    <td class="action">
    <button type="button" class="btn deleterecord" title="Delete Record" data-bs-toggle="modal" data-bs-target="#deleteModalrecord-<?php echo htmlentities($result->id); ?>" data-id="<?php echo htmlentities($result->id); ?>" data-breeder-id="<?php echo htmlentities($result->breeder_id); ?>"> <i class='bx bx-trash'></i></button>

    <button type="button" class="btn btn-sm updateModalBtn" title="Update Pig" data-bs-toggle="modal" data-bs-target="#updateModals" data-feedIds="<?php echo $result->id; ?>"><i class='bx bx-edit'></i></button>

                          </td>
    <!-- Button trigger modal -->
  </tr>
  
<!-- deletepig  Modal -->
<div class="modal fade" id="deleteModalrecord-<?php echo htmlentities($result->id); ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo htmlentities($result->id); ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/deletepig.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this Record?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deleterecord('<?php echo htmlentities($result->id); ?>')" name="deleterecord">Confirm</button>
                    <input type="hidden" id="breederIdHiddenField" value="<?php echo htmlentities($result->breeder_id); ?>">
                </div>
            </div>
        </div>
    </div>

<!-- delete record Modal -->
<!-- update pig Modal -->

<div class="modal fade" id="updateModals" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Pig</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForms" action="breederdetails.php?id=<?php echo $breederId; ?>" method="POST">
          
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="updaterecord" class="btn btn-primary">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>                 
<!-- update pig Modal -->



<?php 
} 
?>	
						</tbody>
					</table>

					<!-- add pig Modal -->
<div class="modal fade" id="confirmModals" tabindex="-1"  aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
<div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Record</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="breederdetails.php?id=<?php echo $breederId; ?>" method="POST">
      <div class="col">
        
  <div class="row">
  <label for="name">Name</label>
    <input type="text" name="name" id="name" class="form-control" placeholder="Pig name" aria-label="First name" autocomplete="given-name" value="<?php echo $pigname ?>" readonly>
  
</div>
  <br>
  <div class="row">
  <label for="farrowed">Farrowed Date</label>
  <input type="date" name="farrowed" id="farrowed" class="form-control"  autocomplete="given-name" required>
  </div>
  <br>
  <div class="row">
  <label for="weaned">Weaned Date</label>
  <input type="date" name="weaned" id="weaned" class="form-control"  autocomplete="given-name" required>
  </div>
  <br>
  <div class="row">
  <label for="total">Total Piglets</label>
  <input type="number" name="total" id="total" class="form-control"  autocomplete="given-name" required>
  </div>
  <br>
  <div class="row">
  <label for="survived">Survived</label>
  <input type="number" name="survived" id="survived" class="form-control"  autocomplete="given-name" required>
  </div>
</div>
<br>
      <div class="modal-footer">
        <div class="col">
            <div class="row mb-1">
            <button type="submit" name="record" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      <div class="row">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                </div>
                </div>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>
        </div>

        	
        </div>
        </section>


		</main>
		<!-- MAIN -->
			<!-- FOOTER -->
		<?php include('includes/footer.php');?>
		<!-- FOOTER -->
	</section>
	<!-- CONTENT -->
	
<script>
// 	$(document).ready(function() {
//     $("#searchInput").on("keyup", function() {
//         var value = $(this).val().toLowerCase();

//         $("#carList li").filter(function() {
//             var combinedData = $(this).data('make') + " " + $(this).data('model') + " " + $(this).data('year');

//             $(this).toggle(combinedData.toLowerCase().indexOf(value) > -1);
//         });
//     });
// 	$('[data-bs-toggle="popover"]').popover();


// });

document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById("formmoved"); 
    const pfemaleinput =document.getElementById("female");
    const pmaleinput =document.getElementById("male"); 
    const totalPiglets = parseInt(document.getElementById("piglets").value)  || 0;
    const errorDiv = document.getElementById("piglet-error");

    form.addEventListener("submit", function (e) {  
        const pmale =  parseInt(pmale.value)||0;
        const pfemale=    parseInt(pfemale.value)  || 0;
        const inputValue = pfemale + pmale ;

        if (inputValue > totalPiglets) {
            e.preventDefault(); 
            errorDiv.classList.remove("d-none"); 
        } else {
            errorDiv.classList.add("d-none"); 
        }
    });


    let forrowingDateString = "<?php echo $pig['forrowingdate']; ?>"; 
    let parts = forrowingDateString.split("-");
    let forrowingDate = new Date(parts[0], parts[1] - 1, parts[2]);

    let currentDate = new Date();
    currentDate.setHours(0,0,0,0); 

    let daysDifference = Math.floor((currentDate - forrowingDate) / (1000 * 60 * 60 * 24));

    let slider = document.getElementById('pigCareSlider');
    slider.disabled = false;
if (daysDifference >= 40) {
    slider.value = 40; // Weaning Day
} else if (daysDifference >= 20) {
    slider.value = 25; // Kapon and Vitamins
} else if (daysDifference >= 2) {
    slider.value = 15; // Iron
} else if (daysDifference >= 1) {
    slider.value = 5; // Vitamins
}

slider.dispatchEvent(new Event('input')); 
slider.disabled = true;
});


document.getElementById('statusSelect').addEventListener('change', function() {
    var forrowingFieldsDiv = document.getElementById('forrowingFields');
    var gestatingFieldsDiv = document.getElementById('gestatingFields');
    var gestatingFieldsDivsm = document.getElementById('gestatingFieldsPigletsmale');
    var gestatingFieldsDivsf = document.getElementById('gestatingFieldsPigletsfemale');
    var gestatingFieldsDivst = document.getElementById('gestatingFieldsPigletstotal');
    var breedingDateInput = document.getElementById('breedingDate');

    if (this.value === 'Farrowing') {
        breedingDateInput.setAttribute('required', true);
        forrowingFieldsDiv.style.display = 'block';  
        gestatingFieldsDiv.style.display = 'none';   
        gestatingFieldsDivsm.style.display = 'none';  
        gestatingFieldsDivsf.style.display = 'none';    
        gestatingFieldsDivst.style.display = 'none';   
        
    } else if (this.value === 'Lactating') {
        breedingDateInput.removeAttribute('required');
        forrowingFieldsDiv.style.display = 'none';   
        gestatingFieldsDiv.style.display = 'block';  
      
        gestatingFieldsDivsm.style.display = 'block';  
        gestatingFieldsDivsf.style.display = 'block';  
        gestatingFieldsDivst.style.display = 'block';   
      
    } else {
        breedingDateInput.removeAttribute('required');
        forrowingFieldsDiv.style.display = 'none';   
        gestatingFieldsDiv.style.display = 'none';   
        gestatingFieldsDivst.style.display = 'none';   
        gestatingFieldsDivsm.style.display = 'none';   
        gestatingFieldsDivsf.style.display = 'none';   
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('myForm').addEventListener('submit', function(e) {
    var statusSelect = document.getElementById('statusSelect').value;
    var forrowingDateInput = document.getElementById('forrowingDate');
    var breedingDateInput = document.getElementById('breedingDate');
    var pigletsInputm = document.getElementById('pigletsm');
    var pigletsInputf = document.getElementById('pigletsf');

    if(statusSelect === 'Lactating' && (!forrowingDateInput.value  || !pigletsInputm.value || !pigletsInputf.value)) {
        e.preventDefault();
        alert('For "Lactating" status, please ensure that both the Farrowing Date and Piglets fields are filled out.');
        return;  
    }
});
});

document.getElementById('pigCareSlider').addEventListener('input', function() {
    let day = parseInt(this.value, 10);
    let tasks = document.querySelectorAll('.care-task');

    tasks.forEach(task => {
        let taskDay = parseInt(task.getAttribute('data-day'), 10); 

        if(taskDay <= day) {
            task.classList.add('active-task');
        } else {
            task.classList.remove('active-task');
        }
    });
});



var deletePigId;
var breederId;

$(document).on('click', '.deleterecord', function() {
    deletePigId = $(this).data('id');
    breederId = $(this).data('breeder-id');

    $('#deleteModalrecord-' + deletePigId).modal('show');
});

$(document).on('click', '#confirmDelete', function() {
    deleterecord(deletePigId);
});

function deleterecord(id) {
    $.ajax({
        url: 'delete.php',
        type: 'POST',
        data: { recordid: id, breeder_id: breederId },
        success: function(response) {
            $('#deleteModalrecord-' + id).modal('hide');
             location.reload();
            
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('An error occurred while trying to delete the record.');
        }
    });
}


$(document).on("click", ".updateModalBtn", function() {
    var feedId = $(this).attr("data-feedIds"); 
  $.ajax({
    url: 'getrecords.php',  
    data: { feedIds: feedId},  
    dataType: 'json',  
    success: function(response) {
      console.log(response);
      $("#updateForms").html(`
      <div class="row">
      <div class="col">
        
        <div class="row">
          <input type="hidden" name="id"  class="form-control" value="${response.id}">
      </div>
        <br>
        <div class="row">
        <label for="farrow">Farrowed Date</label>
        <input type="date" name="farrowed" id="farrow" class="form-control"  autocomplete="given-name" value="${response.date_farrowed}">
        </div>
        <br>
        <div class="row">
        <label for="wean">Weaned Date</label>
        <input type="date" name="weaned" id="wean" class="form-control"  autocomplete="given-name" value="${response.weaned_date}">
        </div>
        <br>
        <div class="row">
        <label for="tot">Total Piglets</label>
        <input type="number" name="total" id="tot" class="form-control"  autocomplete="given-name" value="${response.total_piglets}" min="0">
        </div>
        <br>
        <div class="row">
        <label for="survive">Survived</label>
        <input type="number" name="survive" id="survive" class="form-control"  autocomplete="given-name" value="${response.survived}" min="0">
        </div>
      </div>

    
        
      `);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown); 
    }
  });

});



$(document).on('click', '.delete-btn', function() {
        deletePigId = $(this).data('id');
        $('#deleteModal-' + deletePigId).modal('show');
    });

    $(document).on('click', '#confirmDelete', function() {
        deletepig(deletePigId);
    });

function deletepig(id) {
    $.ajax({
        url: 'delete.php',  
        type: 'POST',
        data: { sowid: id },
        success: function(response) {
        $('#deleteModal-' + id).modal('hide');
        
        window.location.replace('pigbreeders.php');

},
        error: function() {
            alert('An error occurred while trying to delete the sow.');
        }
    });
}




$(document).on('click', '.culling-btn', function() {
        cullingPigId = $(this).data('id');
        $('#cullingModal-' + cullingPigId).modal('show');
    });

    $(document).on('click', '#confirmculling', function() {
        cullingpig(cullingPigId);
    });

function cullingpig(id) {
    $.ajax({
        url: 'delete.php',  
        type: 'POST',
        data: { cullingid: id },
        success: function(response) {
        $('#cullingModal-' + id).modal('hide');
        
        window.location.replace('pigbreeders.php');

},
        error: function() {
            alert('An error occurred while trying to move the sow into culling.');
        }
    });
}


</script>
	<script src="script.js"></script>
</body>
</html>
<?php } ?>