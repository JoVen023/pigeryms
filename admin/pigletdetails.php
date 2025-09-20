<?php
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
		
header('location:index.php');
}
else{
    if (isset($_GET['id']) && isset($_GET['group_id'])) {
        $pigletsId = intval($_GET['id']);
        $groupId = intval($_GET['group_id']);
    } else {
        // Handle error or redirect to another page
        die('ID or group_id not provided.');
    }
 $pigletqr = $dbh->prepare("SELECT img FROM piglets_qr WHERE piglet_id = :id");
$pigletqr->bindparam(':id',$pigletsId,PDO::PARAM_INT);
$pigletqr->execute();
$pigletsqr = $pigletqr->fetch(PDO::FETCH_ASSOC);

$qrImagePath = isset($pigletsqr['img']) ? $pigletsqr['img'] : '';



    $queryDates = "SELECT weaneddate, piggybloom, prestarter, starter, grower, finisher FROM tblgrowingphase tg LEFT JOIN piglets p ON tg.id = p.growinphase_id WHERE tg.id = p.growinphase_id  AND p.id = :pigId ";
$stmtDates = $dbh->prepare($queryDates);
$stmtDates->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
$stmtDates->execute();
$pigDates = $stmtDates->fetch(PDO::FETCH_ASSOC);

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
    $query = "SELECT tg.*, p.*,p.status as pstatus ,(tg.pigs - COUNT(p.growinphase_id)) AS totalpigs 
FROM piglets p
LEFT JOIN tblgrowingphase tg ON p.growinphase_id = tg.id
WHERE p.id = :pigId;
";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $pigletsId, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);

$growingphase_id = $pig['growinphase_id'];


$query = "SELECT COUNT(id) AS totalpigs 
FROM piglets 

WHERE growinphase_id = :growingphase_id;
";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':growingphase_id', $growingphase_id, PDO::PARAM_INT);
$stmt->execute();
$pigtotals = $stmt->fetch(PDO::FETCH_ASSOC);
$totalpigs = $pigtotals['totalpigs'];


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

// age
$weaningDate = new DateTime($pig['weaneddate']);
$currentDate = new DateTime();  
$weaningDate->setTime(0, 0, 0);
$currentDate->setTime(0, 0, 0);
$interval = $currentDate->diff($weaningDate);

$daysDifference = $interval->days;
$age = $daysDifference;
// age


    if ($formattedCurrentDate >= $formattedfinisherDayAfter) {
        $interval = $currentDate->diff($finisher);
        
        $stats = "Finisher";
        $feedConsumptionRate = $pig['pigs'] * 2.2; // average of 2.2kg/day and 2.5kg/day
        $feedsConsumptionRate = $pig['pigs'] *  2.5; // average of 2.2kg/day and 2.5kg/day
        $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
        $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} elseif ($formattedCurrentDate >= $formattedgrowerDayAfter) {
    $stats = "Finisher";
    $feedConsumptionRate = $pig['pigs'] * 2.2; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  2.5; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];

} elseif ($formattedCurrentDate >= $formattedeightyoneDayAfter) {
    $stats = "Grower";
    $feedConsumptionRate = $pig['pigs'] * 1.5; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  2.2; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} elseif ($formattedCurrentDate >= $formattedfiftyoneDayAfter) {
    $stats = "Starter";
    $feedConsumptionRate = $pig['pigs'] * 0.8; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  1.5; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} elseif ($formattedCurrentDate >= $formattedthirtyoneDayAfter) {
    $stats = "Pre-Starter";
    $feedConsumptionRate = $pig['pigs'] * 0.4; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  0.8; // average of 2.2kg/day and 2.5kg/day
    $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
    $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
} else {
    // If none of the above conditions are met, set a default status or don't update
    $stats = "PiggyBloom"; // replace 'DefaultStatus' with whatever default status you want or simply don't set the $stats variable
    $feedConsumptionRate = $pig['pigs'] * 0.02; // average of 2.2kg/day and 2.5kg/day
    $feedsConsumptionRate = $pig['pigs'] *  0.025; // average of 2.2kg/day and 2.5kg/day
   $totalFeed = ($feedConsumptionRate *  $interval->days) / $pig['totalpigs'];
        $totalFeeds = ($feedsConsumptionRate *  $interval->days) / $pig['totalpigs'];
}


// Determine the total sacks needed

// status dates interval
// status dates interval






if (isset($_POST['update'])) {
    $Id = intval($_POST['id']);  
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $groupid = $_POST['group_id'];
    $status = $_POST['stats'];
    $filename = null;

    $fetchQuery = $dbh->prepare("SELECT img FROM piglets WHERE id = :id");
    $fetchQuery->bindParam(':id', $Id, PDO::PARAM_INT);
    $fetchQuery->execute();
    $currentData = $fetchQuery->fetch(PDO::FETCH_OBJ);

    if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) {
        $filename = basename($_FILES['pict']['name']);
        $uploadPath = 'img/' . $filename;
        
        if (!move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {
            $filename = null;
        }
    }

    if (!$filename) {
        $filename = $currentData->img;
    }


   


    if ($status == 'UnHealthy') {
        $dateStarted = !empty($_POST['date_started']) ? $_POST['date_started'] : null;

        $details = $_POST['details'];  
       
        $diagnosedStatus = 'Diagnosed';
        $query1 = $dbh->prepare("INSERT INTO unhealthy_piglets(piglet_id, details, status, date)
                                 VALUES(:piglet_id, :details, :status, :date)");

        $query1->bindParam(':piglet_id', $Id, PDO::PARAM_INT);
        $query1->bindParam(':details', $details, PDO::PARAM_STR);
        $query1->bindParam(':status', $diagnosedStatus, PDO::PARAM_STR);
        $query1->bindParam(':date', $dateStarted, PDO::PARAM_STR);

        $query1->execute();
        $query = $dbh->prepare("UPDATE piglets SET name=:name, status=:status, img=:pict, gender=:gender,posted = 0 WHERE id=:id");
    }
else {
        $query = $dbh->prepare("UPDATE piglets SET name=:name, status=:status, img=:pict, gender=:gender,posted = 0 WHERE id=:id");
    }

    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':gender', $gender, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':pict', $filename, PDO::PARAM_STR);
    $query->bindParam(':id', $Id, PDO::PARAM_INT);

    try {
        $query->execute();
        if($query){
            $success = "Updated Succesfully" && header("refresh:1; url=pigletdetails.php?id=" . $Id ." &group_id=" . $groupid);
        }
            else{
              $error = "Please try again later";
            }

    //         echo "<script type='text/javascript'>
    //     alert('Updated Successfully'); 
    //     window.location.href = 'pigletdetails.php?id=" . $Id . "&group_id=" . $groupid . "';
    // </script>";
    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    }
}


if(isset($_POST['add'])){
    $pigname = $_POST['name'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $growingphase_id = $_POST['id'];
   
    try {
    if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) { 
        $filename = basename($_FILES['pict']['name']);
        $uploadPath = 'img/' . $filename;
        
        if (!move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {
            $filename = null;
        }
    }
  

    $query = $dbh->prepare("INSERT INTO piglets(growinphase_id, name, gender, status, img)VALUES(:growinphase_id, :name, :gender, :status, :pict)");

// Bind all parameters
$query->bindParam(':growinphase_id', $growingphase_id, PDO::PARAM_INT);
$query->bindParam(':name', $pigname, PDO::PARAM_STR);
$query->bindParam(':gender', $gender, PDO::PARAM_STR);
$query->bindParam(':status', $status, PDO::PARAM_STR);
$query->bindParam(':pict', $filename, PDO::PARAM_STR);

$query->execute();

if ($query) {
    echo "<script type='text/javascript'>alert('Added Successfully'); window.location.href = 'growingphasedetails.php?id=" . $growingphase_id . "';</script>";
  } else {
    $err = "Please Try Again Or Try Later";
  }
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    header("Location: growingphasedetails.php?msg=error");
    exit;
    } 

} 

if(isset($_POST['addcull'])){
    $pigname=$_POST['name'];
    $month=$_POST['age'];
    $age = $month . " Months";
    
    if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) { // Check if upload was successful
      // Create a unique filename
      $filename =basename($_FILES['pict']['name']);
    
      // Specify the path to save the uploaded file to
      $uploadPath = 'img/' . $filename;
    
      // Move the uploaded file to the desired directory
      if (move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {
          // Prepare the query
          $query = $dbh->prepare("INSERT INTO tblculling (name,age,status,img) VALUES (:name,:age,'Culling',:pict)");
    
          // Bind the parameters
          $query->bindParam(':name', $pigname, PDO::PARAM_STR);
          $query->bindParam(':age', $age, PDO::PARAM_STR);
          $query->bindParam(':pict', $filename, PDO::PARAM_STR);

          $query2 = $dbh->prepare("UPDATE piglets SET status = 'Cull' , move = 1  WHERE id =:pigletid");
    
          $query2->bindParam(':pigletid', $pigletsId, PDO::PARAM_STR);
       

        }
          // Execute the query
          try {
            $query2->execute();
              $query->execute();
              echo "<script type='text/javascript'>alert('Added Successfully'); window.location.href = 'culling.php';</script>";
          } catch (PDOException $ex) {
              echo $ex->getMessage();
              exit;
          }
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
            <a href="growingphasedetails.php?id=<?= urlencode($groupId); ?>" 
   class="d-inline-block border rounded-2 bg-info text-white px-3 py-2 text-decoration-none">
   <i class='bx bx-left-arrow-circle'></i> Back
</a>
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
        <button type="button" class="btn btn-primary btn-sm " title="Update Pig" data-bs-toggle="modal" data-bs-target="#confirmModal" data-pigid="<?php echo $pig['id']; ?>">Update</button>
    <button type="button" class="btn btn-danger btn-sm <?= ($pig['gender'] =="Female" && $stats =="Finisher" ) ? '':'d-none'; ?>" title="Culling Pig" data-bs-toggle="modal" data-bs-target="#addModal" data-pigid="<?php echo $pig['id']; ?>">Move to Cull</button>
    <button type="button" class="btn btn-success btn-sm <?= ($pig['gender'] =="Female" && $stats =="Finisher" ) ? '':'d-none'; ?>" title="Breeding Pig" data-bs-toggle="modal" data-bs-target="#breederModal" data-pigid="<?php echo $pig['id']; ?>">Move To Breeding</button>
        </div>
        <div class="right-section"> 
            <p class="card-text <?php echo $pig['pstatus']; ?>"><?php echo $pig['pstatus']; ?></p>
        <?php if (!empty($qrImagePath)): ?>
            <a href="#" class="btn btn-sm" 
   onclick="window.open('print_qr.php?img=<?php echo urlencode($qrImagePath); ?>&name=<?php echo urlencode($pig['name']); ?>', 'QRPrint', 'width=800,height=600'); return false;">
    <i class='bx bx-qr-scan'></i>
</a>
<?php endif; ?>
 <a href="printpigletdetails.php?id=<?php echo urlencode($pig['id']);?>" class="btn btn-sm" title="Print Piglets Details" target="_blank">
 <i class='bx bx-printer'></i>
 </a>
        
        <button type="button" class="btn btn-sm deleteModalBtn" title="Delete Pig" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $pig['id']; ?>" data-pigid="<?php echo $pig['id']; ?>" ><i class='bx bx-trash'></i></button>
        
    </div>
    </div>
    <h2 class="card-title"><?php echo $pig['name']; ?></h2>
                <p class="card-text"><span>Gender:</span> <?php echo $pig['gender']; ?></p>
                <p class="card-text"><span>Breed:</span> <?php echo $pig['breed']; ?></p>
                <p class="card-text"><span>Age:</span> <?php echo $age; ?> days</p>
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
            echo 'Sell';
        }
        
    ?></p>
                
            
             
    <p class="card-text"><span>Total Feeds Consumption:</span> <?php echo  round($totalFeed / $totalpigs, 2); ?> - <?php echo round($totalFeeds /  $totalpigs,2); ?> Kilograms</p>

    <!-- CENTERED BUTTONS
      <div class="row">
                <div class="col d-flex justify-content-center">
<button type="button" class="btn btn-primary btn-sm " title="Update Pig" data-bs-toggle="modal" data-bs-target="#confirmModal" data-pigid="<?php echo $pig['id']; ?>">Update</button>
    <button type="button" class="btn btn-danger btn-sm <?= ($pig['gender'] =="Male") ? 'd-none':''; ?>" title="Culling Pig" data-bs-toggle="modal" data-bs-target="#addModal" data-pigid="<?php echo $pig['id']; ?>">Move to Culling</button>
    <button type="button" class="btn btn-success btn-sm <?= ($pig['gender'] =="Male") ? 'd-none':''; ?>" title="Breeding Pig" data-bs-toggle="modal" data-bs-target="#breederModal" data-pigid="<?php echo $pig['id']; ?>">Move To Breeding</button>
    
                </div>
            </div> -->
    <!-- <button type="button" class="btn btn-primary btn-sm " title="Update Pig" data-bs-toggle="modal" data-bs-target="#confirmModal" data-pigid="<?php echo $pig['id']; ?>">Update</button>
    <button type="button" class="btn btn-danger btn-sm <?= ($pig['gender'] =="Female" && $stats =="Finisher" ) ? '':'d-none'; ?>" title="Culling Pig" data-bs-toggle="modal" data-bs-target="#addModal" data-pigid="<?php echo $pig['id']; ?>">Move to Culling</button>
    <button type="button" class="btn btn-success btn-sm <?= ($pig['gender'] =="Female" && $stats =="Finisher" ) ? '':'d-none'; ?>" title="Breeding Pig" data-bs-toggle="modal" data-bs-target="#breederModal" data-pigid="<?php echo $pig['id']; ?>">Move To Breeding</button> -->
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
                        <button type="button" class="btn btn-danger" onclick="deletepig('<?php echo $pig['id']; ?>','<?php echo $groupId; ?>')">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

    <!-- delete pig Modal -->

        <!-- deletepig  Modal -->
        <div class="modal fade" id="qrModal-<?php echo $pig['id']; ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo $pig['id']; ?>" aria-hidden="true">
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
                        <button type="button" class="btn btn-danger" onclick="deletepig('<?php echo $pig['id']; ?>','<?php echo $groupId; ?>')">Confirm</button>
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
                        <input type="hidden" name="id" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pigletsId ?>">
                        <input type="hidden" name="sow_id" class="form-control" value="<?php echo $pigletsId ?>">
                        <input type="hidden" name="group_id" class="form-control" value="<?php echo $groupId ?>">
                        <div class="col">
                            <label for="fsowname">Name</label>
                            <input type="text" id="fsowname" name="name" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pig['name']; ?>" autocomplete="given name">
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" class="form-select form-select-sm" aria-label="Gender">
                                <option selected><?php echo $pig['gender']; ?></option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="status">Status</label>
                            <select name="stats" id="status" class="form-select form-select-sm" aria-label="Status">
                                <option value="Healthy" <?php if ($pig['status'] == 'Healthy') echo 'selected'; ?>>Healthy</option>
                                <option value="UnHealthy" <?php if ($pig['status'] == 'UnHealthy') echo 'selected'; ?>>UnHealthy</option>
                                <option value="Posted" <?php if ($pig['status'] == 'Posted') echo 'selected'; ?>>Posted</option>
                            </select>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col">
                            <label for="map">Picture</label>
                            <input type="file" id="map" name="pict" class="form-control form-control-sm rounded-0">
                        </div>
                    </div>

                    <!-- Hidden fields for UnHealthy status -->
                    <div id="unhealthyFields" style="display:none;">
                        <br>
                        <div class="row">
                            <div class="col">
                                <label for="dateStarted">Date Started</label>
                                <input type="date" id="dateStarted" name="date_started" class="form-control">
                            </div>
                            <div class="col">
                                <label for="details">Details</label>
                                <textarea id="details" name="details" class="form-control" placeholder="Enter details..."></textarea>
                            </div>
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


    	<!-- add pig breeder Modal -->

        <div class="modal fade" id="breederModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Move to Breeder</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="movebreeder.php" method="POST" enctype="multipart/form-data">
      <div class="row">
        
  <div class="col">
  <label for="fullname">Name</label>
  <input type="hidden" name="pigid" class="form-control" placeholder="Pig name" aria-label="First name" value="<?php echo $pigletsId ?>">
    <input type="text" id="fullname" name="name" class="form-control" placeholder="Sow name" aria-label="First name" autocomplete="given-name"  value="<?php echo $pig['name']; ?>">
  </div>
  <div class="col">
  <label for="fullname"># Farrowed</label>
    <input type="number" id="farrowed" name="farrowed" class="form-control" placeholder="How many times Farrowed" aria-label="Farrowed" autocomplete="Farrowed">
  </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="fullname">Age(Month)</label>
          <input type="number" name="age"class="form-control" placeholder="Month" aria-label="Month">
        </div>
        <div class="col">
        <label for="fullname">Status</label>
  <select name="status" id="statusSelect" class="form-select form-select-sm" aria-label="weightclass">
  <option selected>Select</option>
  <option value="Breeding">Breeding</option>
  <option value="Farrowing">Farrowing</option>
  <option value="Lactating">Lactating</option>
</select>
        </div>
</div>
<br>
        
<div class="row">
    <div class="col">
        <!-- Fields for Forrowing -->
        <div id="forrowingFields" style="display: none;">
		
            <label for="breedingDate" class="me-1">Breeding Date:</label>
            <input type="date" name="breedingdate" id="breedingDate" class="me-5">
            
           
        </div>

        <!-- Fields for Lactating -->
        <div id="gestatingFields" style="display: none;">
		<label for="forrowingDate" class="me-1">Farrowing Date:</label>
            <input type="date" name="forrowingdate" id="forrowingDate" class="me-5">
            <label for="piglets" class="me-1">Piglets:</label>
            <input type="number" name="pigs" id="piglets" class="me-3">
        </div>
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
                <button type="submit" name="pigbreeder" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>
                	<!-- add pig breeder Modal -->


    <!-- add culling sow modal -->

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exam   pleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Move Piglet to Culling</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
  <div class="col">
  <label for="sowname">Sow Name</label>
    <input type="text" name="name" id="sowname" class="form-control" placeholder="Pig name" aria-label="name" autocomplete="none"  value="<?php echo $pig['name']; ?>">
  </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="a">Age(Month)</label>
          <input type="number" id="a" name="age"class="form-control" placeholder="Month" aria-label="Month"  required>
        </div>
</div>
<br>

      <div class="row">
      <div class="col">
                                 <label for="map">Picture</label>
  									<input type="file" id="map" name="pict" class="form-control form-control-sm rounded-0" required >
								</div>
</div>

      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="addcull" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>    
<!-- add cull modal -->


    </div>
    </div>
    </div>



    

<section class="records">
<div class="head-title">
				<div class="left">
					<h1>Vaccination Records</h1>
				
				</div>
                
			</div>
        <div class="table-data">
				<div class="order">
                <?php 
                          $sql = "SELECT * FROM vaccines_shot
                          WHERE piglets_id = :piglets_id";
                  
                  $query3 = $dbh->prepare($sql);
                  $query3->bindParam(':piglets_id',$pigletsId, PDO::PARAM_INT);
                  $query3->execute();
                  $results = $query3->fetchAll(PDO::FETCH_OBJ);
                  $totalRows = count($results);
          
                  ?>
				<div class="left">
					<h1>Records Lists</h1>
                    <!-- <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmModals" class="openModalBtn">
  <i class='bx bx-plus-circle'></i> Add New
</button> -->
				</div>
                <table id="myTable">
						<thead>
							<tr>
                                <th  class="text-center">ID</th>
                                <th  class="text-center">Vaccined By</th>
                                <th  class="text-center">Vaccined Name</th>
                                <th  class="text-center">Date Vaccinated</th>
                                <th  class="text-center" >Action</th>
                                
							</tr>
						</thead>
                        
						<tbody>
                   
                     
                  <?php 
                          foreach($results as $result){
                            $date = new DateTime($result->date_vaccinated);
                            $formatteddates = $date->format('F j, Y');
                        
                          
                          ?>
                              
                              <tr>
	<td class="text-center">
	<p><?php echo htmlentities($result->id); ?></p>
		</td>
        <td class="text-center"><?php echo htmlentities($result->vaccined_by); ?></td>
	<td class="text-center"><?php echo htmlentities($result->vaccine_name); ?></td>
	<td><?php echo htmlentities($formatteddates); ?></td>

 
    <!-- Button trigger modal -->
    <td class="action text-center">
    <button type="button" class="btn deleterecord" title="Delete Record" data-bs-toggle="modal" data-bs-target="#deleteModalrecord-<?php echo htmlentities($result->id); ?>" data-id="<?php echo htmlentities($result->id); ?>" data-breeder-id="<?php echo htmlentities($result->breeder_id); ?>"> <i class='bx bx-trash'></i></button>
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
                    <input type="hidden" id="breederIdHiddenField" value="<?php echo htmlentities($result->piglets_id); ?>">
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
      <form  action="add_vaccine.php?id=<?php echo $pigletId; ?>" method="POST">
      <div class="col">
      <div class="row">
  <!-- <label for="name">Vaccine Name</label>
    <input type="text" name="vaccine" id="vaccine" class="form-control" placeholder="Vaccine Name" aria-label="vaccine" autocomplete="given-name">
  
</div> -->
  <div class="row">
  <label for="name">Vaccine Name</label>
    <input type="text" name="vaccine" id="vaccine" class="form-control" placeholder="Vaccine Name" aria-label="vaccine" autocomplete="given-name">
  
</div>
  <br>
  <div class="row">
  <label for="date">Vaccinated Date</label>
  <input type="date" name="date" id="date" class="form-control"  autocomplete="given-name" required>
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

document.addEventListener('DOMContentLoaded', function() {


        const statusSelect = document.getElementById('status');
        const unhealthyFields = document.getElementById('unhealthyFields');
        
        function toggleUnhealthyFields() {
            if (statusSelect.value === 'UnHealthy') {
                unhealthyFields.style.display = 'block'; 
            } else {
                unhealthyFields.style.display = 'none'; 
            }
        }

        toggleUnhealthyFields();

        statusSelect.addEventListener('change', toggleUnhealthyFields);
    });
    
    
    $(document).on('click', '.delete-btn', function() {
            deletePigId = $(this).data('id');
            $('#deleteModal-' + deletePigId).modal('show');
        });

        $(document).on('click', '#confirmDelete', function() {
            deletepig(deletePigId);
        });

        function deletepig(id, growing_phase_id) {
    $.ajax({
        url: 'delete_piglet.php',
        type: 'POST',
        data: {
            pigletsid: id,
            growing_phase_id: growing_phase_id
        },
        success: function(response) {
            $('#deleteModal-' + id).modal('hide');
            // alert('Piglet deleted successfully.'+ growing_phase_id);
            window.location.replace('growingphasedetails.php?id=' + growing_phase_id);
        },
        error: function() {
            alert('An error occurred while trying to delete the piglet.');
        }
    });
}


        $(document).ready(function() {

        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $("#carList li").filter(function() {
                var combinedData = $(this).data('make') + " " + $(this).data('model') + " " + $(this).data('year');

                $(this).toggle(combinedData.toLowerCase().indexOf(value) > -1);
            });
        });
    });

    document.getElementById('statusSelect').addEventListener('change', function() {
    var forrowingFieldsDiv = document.getElementById('forrowingFields');
    var gestatingFieldsDiv = document.getElementById('gestatingFields');

    if (this.value === 'Farrowing') {
        forrowingFieldsDiv.style.display = 'block';  
        gestatingFieldsDiv.style.display = 'none';   
    } else if (this.value === 'Lactating') {
        forrowingFieldsDiv.style.display = 'none';  
        gestatingFieldsDiv.style.display = 'block';  
    } else {
        forrowingFieldsDiv.style.display = 'none';   
        gestatingFieldsDiv.style.display = 'none';   
    }
});

    </script>
        <script src="script.js"></script>
    </body>
    </html>
    <?php } ?>