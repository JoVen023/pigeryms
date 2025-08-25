<?php
include('includes/config.php');
$pigletid = isset($_GET['id']) ? (int) $_GET['id'] : 0;
 
$query = "SELECT tg.*, p.*,p.status as pstatus ,(tg.pigs - COUNT(p.growinphase_id)) AS totalpigs 
FROM piglets p
LEFT JOIN tblgrowingphase tg ON p.growinphase_id = tg.id
WHERE p.id = :pigId;
";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $pigletid, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);


$date = !empty($pig['weaneddate']) ? new DateTime($pig['weaneddate']) : null;
$weaneddate = $date ? $date->format('F j, Y') : null;
// age
$weaningDate = new DateTime($pig['weaneddate']);
$currentDate = new DateTime();  
$weaningDate->setTime(0, 0, 0);
$currentDate->setTime(0, 0, 0);
$interval = $currentDate->diff($weaningDate);

$daysDifference = $interval->days;
$age = $daysDifference;
// age
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Piglet Details</title>
<link rel="icon" type="image/x-icon" href="img/logos.jpeg">
<style>
    img { width: 600px; height: auto; margin-bottom: 20px; border: 2px solid #ccc; border-radius: 10px; }
    h2 { margin-bottom: 20px; }

        body { text-align: center; padding-top: 50px; }
        img { width: 500px; height: 500px; }

    .details { text-align: left; display: inline-block; font-size: 18px; line-height: 1.8; }
    .details span { font-weight: bold; }
</style>
</head>
<body onload="window.print();">

    <h2>Piglet Details</h2>

    <!-- Piglet Image -->
    <img src="img/<?php echo $pig['img']; ?>" width ="" height="" class="img-fluid rounded-start" alt="pig">

    <!-- Piglet Details -->
    <div class="details">
    <p class="card-text"><span>Name:</span> <?php echo $pig['name']; ?></p>
    <p class="card-text"><span>Gender:</span> <?php echo $pig['gender']; ?></p>
                <p class="card-text"><span>Breed:</span> <?php echo $pig['breed']; ?></p>
                <p class="card-text"><span>Age:</span> <?php echo $age; ?> days</p>
                <p class="card-text"><span>Weaned Date:</span> <?php echo $weaneddate ?></p>
                <p class="card-text"><span>Status:</span> <?php echo $pig['status']; ?></p>
             

</body>
</html>
