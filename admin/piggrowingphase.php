<?php
error_reporting(0);
include('includes/config.php');
include 'fetchsow.php';
$_SESSION['sidebarname'] = 'Growing Phase';
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}

else{
   
	$sow = getsowparent($dbh);
if(isset($_POST['add'])){
    $id = intval($_POST['sow']);  // Convert ID to integer
    $pigname = $_POST['name'];
    $pigs = $_POST['piglets'];
    $weaned = $_POST['movingdate'];
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
    $currentDate = new DateTime();
    
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

    $query = $dbh->prepare("INSERT INTO tblgrowingphase(sow_id, sowname, pigs, weaneddate, img, status,piggybloom, prestarter, starter, grower, finisher)VALUES(:id, :name, :pigs, :weaned, :pict, :status,:piggybloom_date, :prestarter_date, :starter_date, :grower_date, :finisher_date)");

// Bind all parameters
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->bindParam(':name', $pigname, PDO::PARAM_STR);
$query->bindParam(':pigs', $pigs, PDO::PARAM_INT);
$query->bindParam(':weaned', $weaned, PDO::PARAM_STR);
$query->bindParam(':status', $stats, PDO::PARAM_STR);
$query->bindParam(':pict', $filename, PDO::PARAM_STR);
$query->bindParam(':piggybloom_date', $thirtyoneDayAfter, PDO::PARAM_STR);
$query->bindParam(':prestarter_date', $fiftyoneDayAfter, PDO::PARAM_STR);
$query->bindParam(':starter_date', $eightyoneDayAfter, PDO::PARAM_STR);
$query->bindParam(':grower_date', $growerDayAfter, PDO::PARAM_STR);
$query->bindParam(':finisher_date', $finisherDayAfter, PDO::PARAM_STR);

$query->execute();

if ($query) {
    $success = "Added Successfully" && header("refresh:1; url=piggrowingphase.php");
  } else {
    $err = "Please Try Again Or Try Later";
  }
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    header("Location: piggrowingphase.php?msg=error");
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
	<title>Pig Growing Phase</title>
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
      

			
<div class="table-data">
			<div class="order">
					<div class="heads">
						<h3>Pigs List</h3>
						<div class="search-container">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Search..." id="searchInput" aria-label="Search">
        <div class="input-group-append">
            <span class="input-group-text"><i class='bx bx-search-alt-2'></i></span>
    </div>
</div>
</div>
						<button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#addModal"
    class="openModalBtn " ><i class='bx bx-plus-circle' style=""></i> Add New</button>
					</div>

					<ul class="breeders" id="carList">
					<?php 
                          
                          $sql ="SELECT * FROM tblgrowingphase order by id desc";
                          $query3 = $dbh->prepare($sql);
                          $query3->execute();
                          $results=$query3->fetchAll(PDO::FETCH_OBJ);
                          
                          foreach($results as $result){

							  $weaneddate = new DateTime($result->weaneddate);
                              $weaneddate =  $weaneddate->format('F j, Y');

                          
                          ?>
                              
					<li data-make="<?php echo htmlentities($result->sowname); ?>" data-model="<?php echo htmlentities($result->status); ?>" data-year="<?php echo htmlentities($result->age); ?>">
    <div class="card">
        <div class="image-container">
            <img src="img/<?php echo htmlentities($result->img); ?>" class="card-img-top" alt="...">
            
            <div class="image-overlay"></div> 
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlentities($result->sowname); ?></h5>
            <div class="flex">
			<p class="card-text <?php echo htmlentities($result->status); ?>"><?php echo htmlentities($result->status); ?></p>
            <p class="card-text"><span>Pigs:</span><?php echo htmlentities($result->pigs);?></p>
  <p class="card-text"><span>Weaned Date:</span><br> <?php echo htmlentities($weaneddate); ?></p>
</div>
<a href="growingphasedetails.php?id=<?php echo htmlentities($result->id); ?>" class="view-btn">View</a>
		</div>
    </div>
</li>
<?php }?>
</ul>
</div>
	
</div>	

<!-- add pig modal -->

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Piglets</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
  <div class="col">
  <label for="pigname">Piglets Name</label>
    <input type="text" id="pigname" name="name" class="form-control" placeholder="Pig name" aria-label="name" autocomplete="off">
  </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="weandate">Weaning Date</label>
          <input type="date" id="weandate" name="movingdate"class="form-control" placeholder="Month" aria-label="Month">
        </div>
        
</div>
<br>
<div class="row">
        <div class="col">
        <label for="piglets" class="me-1">Piglets Survived:</label>
            <input type="number" id="piglets"  name="piglets"  class="me-3">
        </div>
        <div class="col">
        
        <label for="sow">Sow Parent</label>
        <select
                id="sow"
                name="sow"
                class="form-select form-select-sm"
                required="required"
              >
                <?php echo $sow; ?>
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

		</main>
		<!-- MAIN -->
			<!-- FOOTER -->
		<?php include('includes/footer.php');?>
		<!-- FOOTER -->
	</section>
	<!-- CONTENT -->
	
<script>
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

</script>
	<script src="script.js"></script>
</body>
</html>
<?php } ?>