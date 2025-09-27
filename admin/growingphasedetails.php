<?php
error_reporting(0);
include('includes/config.php');
include 'fetchsow.php';
if(strlen($_SESSION['alogin'])==0)
	{	
		
header('location:index.php');
}
else{
        
        if(isset($_GET['id'])) {
            $pigletsId = intval($_GET['id']);
        } else {
            // Handle error or redirect to another page
            die('ID not provided.');
        }
        $piglet = getPiglet($dbh,$pigletsId);

    $queryDates = "SELECT weaneddate, piggybloom, prestarter, starter, grower, finisher FROM tblgrowingphase  WHERE id = :pigId";
$stmtDates = $dbh->prepare($queryDates);
$stmtDates->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
$stmtDates->execute();
$pigDates = $stmtDates->fetch(PDO::FETCH_ASSOC);

if (!$pigDates) {
    $_SESSION['error'] = "No growing phase data found for this piglet.";
    header("Location: piggrowingphase.php");
    exit;
}


$currentDate = new DateTime();
// Format both dates for comparison
$formattedCurrentDates = $currentDate->format('Y-m-d');
$formattedthirtyoneDay = (new DateTime($pigDates['piggybloom']))->format('Y-m-d');
$formattedfiftyoneDay = (new DateTime($pigDates['prestarter']))->format('Y-m-d');
$formattedeightyoneDay = (new DateTime($pigDates['starter']))->format('Y-m-d');
$formattedgrowerDay = (new DateTime($pigDates['grower']))->format('Y-m-d');
$formattedfinisherDay = (new DateTime($pigDates['finisher']))->format('Y-m-d');

if ($formattedCurrentDates >= $formattedfinisherDay) {
    $stat = "Finisher";
} elseif ($formattedCurrentDates >= $formattedgrowerDay) {
    $stat = "Finisher";
} elseif ($formattedCurrentDates >= $formattedeightyoneDay) {
    $stat = "Grower";
} elseif ($formattedCurrentDates >= $formattedfiftyoneDay) {
    $stat = "Starter";
} elseif ($formattedCurrentDates >= $formattedthirtyoneDay) {
    $stat = "Pre-Starter";
} else {
    // If none of the above conditions are met, set a default status or don't update
    $stat = "PiggyBloom"; // replace 'DefaultStatus' with whatever default status you want or simply don't set the $stats variable
}
if (isset($stat)) {
    $updateQuery = "UPDATE tblgrowingphase SET status = :status WHERE id = :pigId";
    $stmt = $dbh->prepare($updateQuery);
    $stmt->bindParam(':status', $stat);
    $stmt->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
    
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error updating status: " . $e->getMessage();
    }
}

// Retrieve the pig details from the database using the $pigId
$query = "SELECT tg.*,
COUNT(CASE WHEN p.gender = 'Female' THEN 1 END) AS totaladded_female,
COUNT(CASE WHEN p.gender = 'Male' THEN 1 END) AS totaladded_male,
(tg.pigs - (SELECT COUNT(*) FROM piglets p2 WHERE p2.growinphase_id =  tg.id AND p2.status != 'Sold')) AS addedpig 
FROM tblgrowingphase tg LEFT JOIN 
piglets p ON tg.id = p.growinphase_id  WHERE tg.id = :pigId  GROUP BY tg.id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);



$male_remaining = $pig['male'] - ($pig['totaladded_male'] ?? 0);
$female_remaining =$pig['female'] -  ($pig['totaladded_female '] ?? 0);

$totaladdedpiglets = empty($pig['addedpig']) ? 'disabled' : '';

$date = !empty($pig['weaneddate']) ? new DateTime($pig['weaneddate']) : null;
$weaneddate = $date ? $date->format('F j, Y') : null;
$piggybloom = !empty($pig['piggybloom']) ? new DateTime($pig['piggybloom']) : null;
$piggybloomdate = $piggybloom ? $piggybloom->format('F j, Y') : null;
$prestarter = !empty($pig['prestarter']) ? new DateTime($pig['prestarter']) : null;
$prestarterdate = $prestarter ? $prestarter->format('F j, Y') : null;
$grower = !empty($pig['grower']) ? new DateTime($pig['grower']) : null;
$growerdate = $grower ? $grower->format('F j, Y') : null;
$starter = !empty($pig['starter']) ? new DateTime($pig['starter']) : null;
$starterdate = $starter ? $starter->format('F j, Y') : null;
$finisher = !empty($pig['finisher']) ? new DateTime($pig['finisher']) : null;
$finisherdate = $finisher ? $finisher->format('F j, Y') : null;

$formattedCurrentDate = $currentDate->format('Y-m-d');
$formattedthirtyoneDayAfter = $piggybloom->format('Y-m-d');
$formattedfiftyoneDayAfter = $prestarter->format('Y-m-d');
$formattedeightyoneDayAfter = $starter->format('Y-m-d');
$formattedgrowerDayAfter = $grower->format('Y-m-d');
$formattedfinisherDayAfter = $finisher->format('Y-m-d');



if ($formattedCurrentDate >= $formattedfinisherDayAfter) {
    $stats = "Finisher";
    $feedConsumptionRate = $pig['pigs'] * 2.2; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  2.5; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = $feedConsumptionRate * 15;
    $totalFeeds = $feedsConsumptionRate * 15;
} elseif ($formattedCurrentDate >= $formattedgrowerDayAfter) {
    $stats = "Finisher";
    $feedConsumptionRate = $pig['pigs'] * 2.2; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  2.5; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = $feedConsumptionRate * 15;
    $totalFeeds = $feedsConsumptionRate * 15;

} elseif ($formattedCurrentDate >= $formattedeightyoneDayAfter) {
    $stats = "Grower";
    $feedConsumptionRate = $pig['pigs'] * 1.5; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  2.2; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = $feedConsumptionRate * 50;
$totalFeeds = $feedsConsumptionRate * 50;
} elseif ($formattedCurrentDate >= $formattedfiftyoneDayAfter) {
    $stats = "Starter";
    $feedConsumptionRate = $pig['pigs'] * 0.8; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  1.5; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = $feedConsumptionRate * 30;
$totalFeeds = $feedsConsumptionRate * 30;
} elseif ($formattedCurrentDate >= $formattedthirtyoneDayAfter) {
    $stats = "Pre-Starter";
    $feedConsumptionRate = $pig['pigs'] * 0.4; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  0.8; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = $feedConsumptionRate * 20;
$totalFeeds = $feedsConsumptionRate * 20;
} else {
    // If none of the above conditions are met, set a default status or don't update
    $stats = "PiggyBloom"; // replace 'DefaultStatus' with whatever default status you want or simply don't set the $stats variable
    $feedConsumptionRate = $pig['pigs'] * 0.02; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  0.025; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = $feedConsumptionRate * 31;
$totalFeeds = $feedsConsumptionRate * 31;
}


// Determine the total sacks needed

// status dates interval
// status dates interval

// age
$weaningDate = new DateTime($pig['weaneddate']);
$currentDate = new DateTime();  
$weaningDate->setTime(0, 0, 0);
$currentDate->setTime(0, 0, 0);
$interval = $currentDate->diff($weaningDate);

$daysDifference = $interval->days;
$age = $daysDifference;
// age




if(isset($_POST['update'])){
    $Id = intval($_POST['id']);  // Convert ID to integer
    $pigname = $_POST['name'];
    $sow_id = $_POST['sow_id'];
    $pigs = $_POST['pigs'];
    $mortality = $_POST['mortality'];
    $stat = $_POST['stats'];
    $filename = null;
    $total_pigs=$pigs-$mortality;
    // Fetch current data from database
    $fetchQuery = $dbh->prepare("SELECT * FROM tblgrowingphase WHERE id = :id");
    $fetchQuery->bindParam(':id', $Id, PDO::PARAM_STR);
    $fetchQuery->execute();
    $currentData = $fetchQuery->fetch(PDO::FETCH_OBJ);
    $currentstatus = $currentData->status;
    
    if ($currentstatus != $stat) { 
        $currentDate = new DateTime();
        // If the weaned date has changed
        if ($stat == 'PiggyBloom') {
    $currentDate->add(new DateInterval('P31D')); // Add 32 day
    $thirtyoneDayAfter = $currentDate->format('Y-m-d');
    $currentDate->add(new DateInterval('P20D')); // Add 20 day
    $fiftyoneDayAfter = $currentDate->format('Y-m-d');
    $currentDate->add(new DateInterval('P30D')); // Add 30 day
    $eightyoneDayAfter = $currentDate->format('Y-m-d');
    $currentDate->add(new DateInterval('P50D')); // Add 50 day
    $growerDayAfter = $currentDate->format('Y-m-d');
    $currentDate->add(new DateInterval('P15D')); // Add 15 day
    $finisherDayAfter = $currentDate->format('Y-m-d');
    $status='PiggyBloom';

        }
       elseif ($stat == 'Pre-Starter') {
            $currentDate->add(new DateInterval('P20D')); // Add 20 day
            $fiftyoneDayAfter = $currentDate->format('Y-m-d');
            $currentDate->add(new DateInterval('P30D')); // Add 30 day
            $eightyoneDayAfter = $currentDate->format('Y-m-d');
            $currentDate->add(new DateInterval('P50D')); // Add 50 day
            $growerDayAfter = $currentDate->format('Y-m-d');
            $currentDate->add(new DateInterval('P15D')); // Add 15 day
            $finisherDayAfter = $currentDate->format('Y-m-d');
            $status='Pre-Starter';
        } elseif ($stat == 'Starter') {
            $currentDate->add(new DateInterval('P30D')); // Add 30 day
            $eightyoneDayAfter = $currentDate->format('Y-m-d');
            $currentDate->add(new DateInterval('P50D')); // Add 50 day
            $growerDayAfter = $currentDate->format('Y-m-d');
            $currentDate->add(new DateInterval('P15D')); // Add 15 day
            $finisherDayAfter = $currentDate->format('Y-m-d');
            $status='Starter';
        } elseif ($stat == 'Grower') {
            $currentDate->add(new DateInterval('P50D')); // Add 50 day
            $growerDayAfter = $currentDate->format('Y-m-d');
            $currentDate->add(new DateInterval('P15D')); // Add 15 day
            $finisherDayAfter = $currentDate->format('Y-m-d');
            $status='Grower';
            
        } 
        elseif ($stat == 'Sold'){
            $thirtyoneDayAfter = $currentData->piggybloom;
            $fiftyoneDayAfter  = $currentData->prestarter;
            $eightyoneDayAfter = $currentData->starter;
            $growerDayAfter    = $currentData->grower;
            $finisherDayAfter  = $currentData->finisher;
            $status='Sold';
        } elseif ($stat == 'Finisher') {
            $currentDate->add(new DateInterval('P15D')); // Add 15 day
            $finisherDayAfter = $currentDate->format('Y-m-d');
                $status='Finisher';
        }else{
            $status==$stat;
        }
    } else {
    $thirtyoneDayAfter = $piggybloom->format('Y-m-d');
    $fiftyoneDayAfter = $prestarter->format('Y-m-d');
    $eightyoneDayAfter = $starter->format('Y-m-d');
    $growerDayAfter = $grower->format('Y-m-d');
    $finisherDayAfter = $finisher->format('Y-m-d');
        $status = $stat;
    }

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
    $query1 = $dbh->prepare("UPDATE breeder_records SET survived=:survived WHERE breeder_id=:sow_id");
    $query1->bindParam(':sow_id', $sow_id, PDO::PARAM_STR);
    $query1->bindParam(':survived', $total_pigs, PDO::PARAM_STR);
    $query1->execute();


        $query = $dbh->prepare("UPDATE tblgrowingphase SET sowname=:name, status=:status, img=:pict, pigs=:pigs,mortality=:mortality,piggybloom=:piggybloom_date,prestarter=:prestarter_date,starter=:starter_date,grower=:grower_date,finisher=:finisher_date WHERE id=:id");
        $query->bindParam(':name', $pigname, PDO::PARAM_STR);
        $query->bindParam(':pigs', $total_pigs, PDO::PARAM_STR);
        $query->bindParam(':mortality', $mortality, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':pict', $filename, PDO::PARAM_STR);
        $query->bindParam(':id', $Id, PDO::PARAM_INT);
        $query->bindParam(':piggybloom_date', $thirtyoneDayAfter, PDO::PARAM_STR);
        $query->bindParam(':prestarter_date', $fiftyoneDayAfter, PDO::PARAM_STR);
        $query->bindParam(':starter_date', $eightyoneDayAfter, PDO::PARAM_STR);
        $query->bindParam(':grower_date', $growerDayAfter, PDO::PARAM_STR);
        $query->bindParam(':finisher_date', $finisherDayAfter, PDO::PARAM_STR);

       


    // Execute the query
    try {
      
        $query->execute();
      

        if ($query) {
            
        $success = "Updated Successfully" && header("refresh:1;url=growingphasedetails.php?id=" . $Id);
    
        // $success = "Updated Successfully";
            // header("Refresh: 1; url=growingphasedetails.php?id=" . $Id);
            // exit; 
        } else {
            $error = "Please try again later";
        }

//   echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'growingphasedetails.php?id=" . $Id . "';</script>";

    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    } 

}




if (isset($_POST['sellpiglets'])) {
    $growingphase_id = $_POST['id'];
    $name = $_POST['name'];
    
    $farrowed_Date = $_POST['farrowed'];
    $piglet_prices_json = $_POST['piglet-prices'] ?? null;
    $piglet_prices = json_decode($piglet_prices_json, true);

    $totalprice = 0;
    if ($piglet_prices) {
        foreach ($piglet_prices as $pigletdetails) {
            $totalprice += $pigletdetails['price'];
        }
    }

    // insert parent sale record
    $stmt = $dbh->prepare("INSERT INTO tblpiglet_for_sale
        (growingphase_id, name, farrowed_Date, price, status, created)
        VALUES (:growingphase_id, :name, :farrowed_Date, :price, 'AVAILABLE', CURDATE())");

    $stmt->bindParam(':growingphase_id', $growingphase_id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':farrowed_Date', $farrowed_Date, PDO::PARAM_STR);
    $stmt->bindParam(':price', $totalprice, PDO::PARAM_INT);
    $stmt->execute();

    $lasttblpiglet_for_sale_id = $dbh->lastInsertId();

    $updategroup = $dbh->prepare("UPDATE tblgrowingphase SET posted = TRUE WHERE id = :id");

    $updategroup->execute([':id'=>$growingphase_id]);
    
    try {
        $stmtinsertpigletdetails = $dbh->prepare("INSERT INTO tblpiglet_for_sale_details
        (tblpiglet_for_sale_id,name,piglet_id, price, piglet_weight, gender, img, status, created)
        VALUES (:tblpiglet_for_sale_id,:name,:piglet_id, :price, :weight, :gender, :img, 'AVAILABLE', CURDATE())");


$stmtupdatetpigletdetails = $dbh->prepare("UPDATE piglets SET posted = 1 WHERE id = :piglet_id");
    

        foreach ($piglet_prices as $i => $pigletdetails_added) {
            // match file by index
            if (isset($_FILES['pictpiglets']['tmp_name'][$i]) && $_FILES['pictpiglets']['error'][$i] === UPLOAD_ERR_OK) {
                $fileName  = basename($_FILES['pictpiglets']['name'][$i]);
                $tmpName   = $_FILES['pictpiglets']['tmp_name'][$i];
                $uploadDir = 'img/img_piglets_for_sale/';
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $stmtinsertpigletdetails->execute([
                        ':tblpiglet_for_sale_id' => $lasttblpiglet_for_sale_id,
                        ':name'  => $pigletdetails_added['name'],
                        ':piglet_id'  => $pigletdetails_added['piglet_id'],
                        ':price'  => $pigletdetails_added['price'],
                        ':weight' => $pigletdetails_added['weight'],
                        ':gender'  => $pigletdetails_added['pigletgender'],
                        ':img'    => $fileName
                    ]);

                    $stmtupdatetpigletdetails->execute([
                        ':piglet_id' => $pigletdetails_added['piglet_id']
                    ]);
                }
            }
        }

        echo "<script>alert('Piglets Posted successfully!'); 
              window.location.href='growingphasedetails.php?id=" . $growingphase_id . "';</script>";
    } catch (PDOException $e) {
        header("Location: growingphasedetails.php?id=$growingphase_id&error=" . urlencode("PDO Exception: " . $e->getMessage()));
        exit;
    }
}
          
	?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Pig</title>
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
                        <h1>Feeding Guide</h1>
                    
                    </div>
                </div>

                
                <div class="feedingguide">
                
            <figure>
            <img src="img/<?php echo $stats?>.png" class="img-fluid rounded-start" alt="starter">
    </figure>
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
        <h2 class="card-title"><?php echo $pig['sowname']; ?></h2>



            
        </div>
        <div class="right-section"> <!-- A container for the trash icon -->
        <p class="card-text <?php echo $stats?>"> <?php echo $stats ?></p>
        <button type="button" class="btn btn-sm deleteModalBtn" title="Delete Pig" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $pig['id']; ?>" data-pigid="<?php echo $pig['id']; ?>" ><i class='bx bx-trash'></i></button><span></span>
        </div>
    </div>
                <p class="card-text"><span>Age:</span> <?php echo $age; ?> days</p>
                <p class="card-text"><span>Male:</span> <?php echo $pig['male']; ?> </p>
                <p class="card-text"><span>Female:</span> <?php echo $pig['female']; ?> </p>
                <p class="card-text"><span>Total Pigs:</span> <?php echo $pig['pigs']; ?></p>
                <p class="card-text"><span>Mortality:</span> <?php echo $pig['mortality']; ?></p>
                
                <p class="card-text"><span>Weaned Date:</span> <?php echo $weaneddate ?></p>
            
                <p class="card-text"><span>Proceed to <?php 
        if ($pig['status'] == "PiggyBloom") {
            echo "Pre-Starter:</span> $piggybloomdate ";
        } elseif ($pig['status'] == "Pre-Starter") {
            echo "Starter:</span> $prestarterdate";
        }
        elseif ($pig['status'] == "Starter") {
            echo "Grower:</span> $starterdate";
        }
        elseif ($pig['status'] == "Grower") {
            echo "Finisher:</span> $growerdate";
        }
        elseif ($pig['status'] == "Finisher") {
            echo "Completed:</span> $finisherdate";
        }
        else{
            echo $pig['status'];
        }
        
    ?></p>
    <p class="card-text"><span>Total Feeds Consumption:</span> <?php echo $totalFeed ?> - <?php echo $totalFeeds ?> Kilograms</p>
    <br>
 <div class="buttons-section d-flex justify-content-center">
 <button type="button" class="btn btn-md btn-primary me-2 " 
            title="Update Pig" data-bs-toggle="modal" 
            data-bs-target="#confirmModal" 
            data-pigid="<?php echo $pig['id']; ?>">
      Update
    </button>

    <button type="button" 
            class="btn btn-md btn-danger  <?= ($stat == "PiggyBloom") ? '' : 'd-none' ?>" 
            title="Sell Piglets" data-bs-toggle="modal" 
            data-bs-target="#sellModal" 
            data-pigid="<?php echo $pig['id']; ?>" 
            data-totalpiglets="<?php echo $pig['pigs']; ?>">
      Sell Piglets
    </button>
 </div>


     
  


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
                        <h3 class="confirm">Are you sure you want to delete this pig?</h3>
                    </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" onclick="deletepig('<?php echo $pig['id']; ?>')">Confirm</button>
                        
                    </div>
                </div>
            </div>
        </div>

    <!-- delete pig Modal -->
    </div>


        </div>
        <!-- update pig Modal -->

        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header custom-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Update Pigs</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
        <form id="myForm" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
        <div class="row">
        <input type="hidden" name="id" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['id']; ?>">
        <input type="hidden" name="sow_id" class="form-control"  value="<?php echo $pig['sow_id']; ?>">
        <div class="col">
    <label for="fsowname">Name</label>
        <input type="text" id="fsowname" name="name" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['sowname']; ?>" autocomplete="given name">
    </div>
    <div class="col">
    <label for="pig">Number of Pigs</label>
        <input type="number" name="pigs" id="pig" class="form-control" placeholder="Pigs" aria-label="pigs" value="<?php echo $pig['pigs']; ?>" min="0" readonly>
    </div>

    </div>

    <br>
    <div class="row">
            <div class="col">
            <label for="win">Weaning Date</label>
        <input type="date" name="weaned" id="win" class="form-control" placeholder="weaned date" aria-label="weaned date" value="<?php echo $pig['weaneddate']; ?>" readonly>
            </div>
            <div class="col">
    <label for="sts">Status</label>
    <select name="stats" id="sts" class="form-select form-select-sm" aria-label="status">
        <option selected><?php echo $stats ?></option>
        <?php
        $statusOptions = ['PiggyBloom', 'Pre-Starter', 'Starter', 'Grower', 'Finisher', 'Sold'];

        foreach ($statusOptions as $status) {
            if ($status != $stats) {
                echo "<option value=\"$status\">$status</option>";
            }
        }
        ?>
    </select>
</div>
    </div>
    <div class="row">
    <div class="col">
    <!-- <label for="pig">Mortality</label> -->
        <input type="number" name="mortality" id="mortality" class="form-control" placeholder="Mortality" aria-label="mortality" value="<?php echo $pig['mortality']; ?>" hidden>
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



     <!-- sell pig Modal -->

     <div class="modal fade" id="sellModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header custom-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Sell Piglets</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
        <form id="myForm" action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
        <div class="row">
        <input type="hidden" name="id" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['id']; ?>">
        <input type="hidden" name="sow_id" class="form-control"  value="<?php echo $pig['sow_id']; ?>">
        <div class="col">
        <label for="pigletsgrouplist">Piglet</label>
        <select
              id="pigletsgrouplist"
              name="name"
              class="form-select form-select-sm"
              required="required"
              onchange="pigletsgroupchange()">
            
              <?php echo $piglet; ?>
            </select>
    </div>
    <div class="col">
            <label for="win">Farrowed Date</label>
        <input type="date" name="farrowed" id="farrowed" class="form-control" placeholder="farrowed date" aria-label="farrowed date" value="<?php echo $pig['weaneddate']; ?>" readonly>
            </div>

    </div>
    <br>
    <div class="row">
        <h5>Add Price per Piglet</h5>
    </div>
    <div class="row">
    <div class="col">
    <label for="pictpiglets">Picture</label>
    <input type="file" id="pictpiglets" name="pictpiglets[]" class="form-control" multiple>
    <input type="hidden" id="piglet_id" name="piglet_id" class="form-control">
    <input type="hidden" id="piglet-prices" name="piglet-prices" class="form-control form-control-sm rounded-0">
</div>
<div class="col">

<label for="pigletgender">Gender</label>
    <input type="text" id="pigletgender" name ="pigletgender" class="form-control" placeholder="Male/Female" disabled>


<!-- <label for="pigletgender">Gender</label>
<select name="pigletgender" id="pigletgender" class="form-select form-select-sm" aria-label="weightclass">
  <option selected>Select</option>
  <option value="Male">Male</option>
  <option value="Female">Female</option>
</select> -->


   
</div>
<div class="col">
    <label for="weight">Weight</label>
    <input type="number" id="piglet_weight" name ="weight" class="form-control" placeholder="Kg"  min="0">
</div>

<div class="col">
    <label for="priceInput">Price</label>
    <input type="number" id="priceInput" name = "priceInput" class="form-control" placeholder="Pesos"  min="0">
</div>

<div class="col d-flex flex-column">
    <input type="button" class="form-control btn btn-dark mt-auto" id="price-add" value="ADD">
</div>

    </div>
    <div class="row ">
    </div>
    <br>
    <div class="row">
<div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Weight</th>
                            <th scope="col">Price</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody id="piglets-children">
                        </tbody>
                      </table>
                    </div>
</div>

        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="sellpiglets" class="btn btn-primary" id="confirmBtn">Confirm</button>
        </div>
        </form>
        </div>
    </div>
                    </div>

                    </div>    
    <!-- sell pig Modal -->


    </div>
    </div>
    </div>


    <div class="table-data" >
                <div class="order">
                        <div class="heads">
                            <h3>Piglets List</h3>
                            <div class="search-container">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search..." id="searchInput" aria-label="Search">
            <div class="input-group-append">
                <span class="input-group-text"><i class='bx bx-search-alt-2'></i></span>
        </div>
    </div>
    </div>
                            <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#addModal"
        class="openModalBtn" <?php echo $totaladdedpiglets ?>><i class='bx bx-plus-circle' ></i> Add New </button>
                        </div>

                        <ul class="breeders" id="carList">
                        <?php 
                            
                            $sql ="SELECT p.*, tfsd.status AS piglet_status 
                            FROM piglets p 
                            LEFT JOIN tblpiglet_for_sale_details tfsd 
                                 ON p.id = tfsd.piglet_id 
                            WHERE p.growinphase_id = :pigid 
                              AND p.status NOT IN('Cull','UnHealthy','Breeder')
                            ORDER BY p.id DESC";

                            $query3 = $dbh->prepare($sql);
                            $query3->bindparam(':pigid',$pigletsId ,PDO::PARAM_INT);
                            $query3->execute();
                            $results=$query3->fetchAll(PDO::FETCH_OBJ);
                            
                            foreach($results as $result){
                                if ($result->piglet_status == "ordered"){
                                    $piglets_status = 'Sold';
                                }elseif($result->posted == 1){
                                    $piglets_status = 'Posted';
                                }else{
                                    $piglets_status =$result->status;
                                }

                            ?>
                                
                        <li data-make="<?php echo htmlentities($result->name); ?>" data-model="<?php echo htmlentities($result->status); ?>" data-year="<?php echo htmlentities($result->age); ?>">
        <div class="card">
            <div class="image-container">
                <img src="img/<?php echo htmlentities($result->img); ?>" class="card-img-top" alt="...">
                
                <div class="image-overlay"></div> 
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlentities($result->name); ?></h5>
                <div class="flex">
                <p class="card-text <?= $piglets_status; ?>">
    <?= $piglets_status; ?>
</p>

                <p class="card-text"><span>Gender: &nbsp;</span><?php echo htmlentities($result->gender);?></p>
    <p class="card-text"><span>Feed Intake:</span><br> <?php echo $totalFeeds ?> kg</p>
    </div>
    <?php 
if ($result->piglet_status == "ordered") {
    echo '<a href="#" class="view-btn disabled-link">Sold</a>';
} elseif($piglets_status == "Posted") {
    echo '<a href="#" class="view-btn disabled-link">Posted</a>';
}else{
    echo '<a href="'
    .($result->status == "Cull" ?'culling.php':'pigletdetails.php?id=' . htmlentities($result->id) . '&group_id=' . htmlentities($pigletsId)) . '" class="view-btn">View</a>';
}
?>

 
            </div>
        </div>
    </li>
    <?php }?>
    </ul>
     <!-- add pig modal -->

     <div class="modal fade" id="addModal"  aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header custom-header">
            <h1 class="modal-title fs-5" id="addModalLabel">Add Piglets</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            
        <form action="addpiglets.php" method="POST" enctype="multipart/form-data">
        <div class="row">
    <div class="col">
    <label for="pigname">Piglets Name</label>
        <input type="text" id="pigname" name="name" class="form-control" placeholder="Pig name" aria-label="name" autocomplete="off" required>
        <input type="text" id="id" name="id" class="form-control" placeholder="Pig name" aria-label="name" value="<?php echo $pigletsId; ?>" autocomplete="off"  hidden>
    </div>
    </div>
    <br>
    <div class="row">
    <div class="col">
    <label for="Breed">Breed</label>
  <select name="breed" id="breed" class="form-select form-select-sm" aria-label="breedclass" required>
  <option  value="" disabled selected>Select</option>
  <option value="Landrace">Landrace</option>
  <option value="Duroc">Duroc</option>
  <option value="Hampshire"><?=$female_remaining?>Hampshire</option>
  <option value="Pietrain"><?=$male_remaining?>Pietrain</option>
</select>
    </div>
    </div>
    <br>
    <div class="row">
  <div class="col">
    <label>Gender: &nbsp;</label>

    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" 
             name="gender" id="Male" value="Male" 
             <?= ($male_remaining <= 0 ? 'disabled' : '') ?> required>
      <label class="form-check-label" for="Male">Male</label>
    </div>

    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" 
             name="gender" id="Female" value="Female" 
             <?= ($female_remaining <= 0 ? 'disabled' : '') ?>>
      <label class="form-check-label" for="Female">Female</label>
    </div>

  </div>
</div>
    <br>
    <div class="row">
            
            <div class="col">
            <label for="weandate">Status: &nbsp;</label>
            <input class="form-check-input" type="radio" name="status" id="Healthy" value="Healthy" required>
  <label class="form-check-label" for="Healthy">
  Healthy
  </label>

  <input class="form-check-input" type="radio" name="status" id="UnHealthy" value="UnHealthy" >
  <label class="form-check-label" for="UnHealthy">
  UnHealthy
  </label>

            </div>
            
    </div>



<div class="row" id="unhealthyFields" >
    <div class="col">
        <label for="details">Details</label>
        <input type="text" id="details" name="details" class="form-control" placeholder="Enter details">
    </div>
    <div class="col">
        <label for="date_started">Date Started</label>
        <input type="date" id="date_started" name="date_started" class="form-control">
    </div>
</div>



    <br>
        <div class="row">
        <div class="col">
                                    <label for="map">Picture</label>
                                        <input type="file" id="map" name="pict" class="form-control form-control-sm rounded-0"required>
                                    </div>
    </div>

        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary" id="confirmBtn">Confirm</button>
        </div>
        </form>
        </div>
    </div>
                    </div>

                    </div>    
    <!-- add pig modal -->
        
    </div>
    </div>	

            </main>
            <!-- MAIN -->
                <!-- FOOTER -->
            <?php include('includes/footer.php');?>
            <!-- FOOTER -->
        </section>
        <!-- CONTENT -->
        
    <script>

function pigletsgroupchange() {
    let pigletIdField = document.getElementById('piglet_id');
    let genderField = document.getElementById('pigletgender');

    var pigletsgroupselect = document.getElementById('pigletsgrouplist');
    var selectedpigletId = pigletsgroupselect.value;

    if (selectedpigletId) {
        fetch('getChildOptions.php?piglet_id=' + selectedpigletId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data && !data.error) {
                    genderField.value = data.gender;  // assuming gender is a string
                    pigletIdField.value = data.id;
                } else if (data.error) {
                    console.log('Warning:', data.error);
                    alert('Warning: ' + data.error);
                } else {
                    console.log('Unexpected response:', data);
                    alert('Unexpected error occurred.');
                }
            })
            .catch(error => {
                console.log('Error fetching child options:', error);
                alert('An error occurred while fetching child options.');
            });
    }
}


    document.addEventListener("DOMContentLoaded", function () {

        let addpigletsprice = document.getElementById("price-add");
        let pigletsdetails= [];



addpigletsprice.addEventListener('click', ()=>{
    let piglet_id = document.getElementById("piglet_id").value;
            let img = document.getElementById("pictpiglets");
            let pigletSelect = document.getElementById("pigletsgrouplist");
            let pigletId = pigletSelect.value;
            let name = pigletSelect.options[pigletSelect.selectedIndex].text;
            let weight = document.getElementById("piglet_weight");
            let pigletgender = document.getElementById("pigletgender");
            let price = document.getElementById("priceInput");

 if(!img.files.length){
    alert("Please add an image.");
    return;
 }
 if(!weight.value){
    alert("Please input a weight.");
    return;
 }
 if (!pigletId) {
    alert("Please select a name.");
    return;
}
 if(!pigletgender.value){
    alert("Please input a gender.");
    return;
 }

 if (!price.value){
    alert("Please input a price.");
    return;
 }

 let fileIndex = pigletsdetails.length;
 let rawFileName = img.files[0].name;
let newpiglets = {
    "piglet_id": piglet_id,
    "name": name,
    "img": rawFileName,
    "weight":weight.value,
    "pigletgender":pigletgender.value,
    "price":price.value,
    "fileIndex": fileIndex
}
        pigletsdetails.push(newpiglets);
        document.getElementById("piglet-prices").value = JSON.stringify(pigletsdetails);



        let piglets_child = document.getElementById("piglets-children");
        let pigletrow = document.createElement("tr");


        let td1 = document.createElement("td");
        let td2 = document.createElement("td");
        let td3 = document.createElement("td");
        let td4 = document.createElement("td");
        let td5= document.createElement("td");
        let td6 = document.createElement("td");

        let previewUrl = URL.createObjectURL(img.files[0]);
        let pigletimg = document.createElement("img");
        pigletimg.src = previewUrl;
        pigletimg.width = 60;
        pigletimg.height = 60;
        td1.appendChild(pigletimg);

        let fileInputClone = img.cloneNode();
fileInputClone.name = "pictpiglets[]";
fileInputClone.style.display = "none";
fileInputClone.files = img.files;
td1.appendChild(fileInputClone);

        td2.innerText =  newpiglets.name;
        td4.innerText = newpiglets.weight;
        td5.innerText = newpiglets.price;
        td3.innerText = newpiglets.pigletgender;

        [td1,td2,td3,td4,td5,td6].forEach(td=>td.classList.add("text-center"));


        let removebutton = document.createElement('button');
        removebutton.innerText = "Remove";
        removebutton.classList.add("btn","btn-dark");   
        removebutton.addEventListener("click",()=>{
            pigletrow.remove();
            pigletsdetails = pigletsdetails.filter(p => p !== newpiglets);
    document.getElementById('piglet-prices').value = JSON.stringify(pigletsdetails);


        });

        td6.appendChild(removebutton);
        pigletrow.appendChild(td1);
        pigletrow.appendChild(td2);
        pigletrow.appendChild(td3);
        pigletrow.appendChild(td4);
        pigletrow.appendChild(td5);
        pigletrow.appendChild(td6);

        piglets_child.appendChild(pigletrow);

        document.getElementById('piglet-prices').value = JSON.stringify(pigletsdetails);

        weight.value = "";
        price.value = "";
        pigletgender.value = "";
        });
    











        const unhealthyRadio = document.getElementById("UnHealthy");
        const healthyRadio = document.getElementById("Healthy");
        const extraFields = document.getElementById("unhealthyFields");

        function toggleUnhealthyFields() {
            if (unhealthyRadio.checked) {
                extraFields.style.display = "block";
            } else {
                extraFields.style.display = "none";
            }
        }

        unhealthyRadio.addEventListener("change", toggleUnhealthyFields);
        healthyRadio.addEventListener("change", toggleUnhealthyFields);
    });


        $(document).ready(function() {

        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $("#carList li").filter(function() {
                // Combining all the data attributes into one string for comparison
                var combinedData = $(this).data('make') + " " + $(this).data('model') + " " + $(this).data('year');

                // Toggle the visibility based on whether the combined data string contains the search term
                $(this).toggle(combinedData.toLowerCase().indexOf(value) > -1);
            });
        });
    });

    $(document).on('click', '.delete-btn', function() {
            // Save the pig ID to deletePigId
            deletePigId = $(this).data('id');
            // Show the modal
            $('#deleteModal-' + deletePigId).modal('show');
        });

        // Handle the "Confirm" button click
        $(document).on('click', '#confirmDelete', function() {
            // Call deletepig
            deletepig(deletePigId);
        });

    function deletepig(id) {
        // Send a POST request to delete.php
        $.ajax({
            url: 'delete.php',  // This sends the request to delete.php
            type: 'POST',
            data: { pigsid: id },
            success: function(response) {
            // Show the success message
            // Close the modal
            $('#deleteModal-' + id).modal('hide');
            
            // Redirect to the desired page
            window.location.replace('piggrowingphase.php');

    },
            error: function() {
                alert('An error occurred while trying to delete the sow.');
            }
        });
    }


    </script>
        <script src="script.js"></script>
    </body>
    </html>
    <?php } ?>