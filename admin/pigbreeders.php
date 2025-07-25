<?php
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}


else{
	$_SESSION['sidebarname'] ='Pig Breeders';
	if(isset($_POST['pig'])){
		$pigname=$_POST['name'];
		$month=$_POST['age'];
		$pigs=$_POST['pigs'];
		$farrowed=$_POST['farrowed'];
		$forrowingdate=$_POST['forrowingdate'];
		$forrowingDateTime = new DateTime($forrowingdate);
$forrowingDateTime->add(new DateInterval('P40D'));  // Adds 30 days
$newDate = $forrowingDateTime->format('Y-m-d'); 

		$breedingdate=$_POST['breedingdate'];
		$breedingdateTime = new DateTime($breedingdate);
		$breedingdateTime->add(new DateInterval('P116D'));  // Adds 120 days
		$newDates = $breedingdateTime->format('Y-m-d'); 

		$age = $month . " Months";
		$status=$_POST['status'];
	
		
		if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) { // Check if upload was successful
		  // Create a unique filename
		  $filename =basename($_FILES['pict']['name']);
		
		  // Specify the path to save the uploaded file to
		  $uploadPath = 'img/' . $filename;
		
		  // Move the uploaded file to the desired directory
		  if (move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {
		   
			if ($status == "Farrowing") {
			  // Prepare the query
			  $query = $dbh->prepare("INSERT INTO tblpigbreeders (name,age, status,total_farrowed,img,breedingstart,forrowingdate) VALUES (:name,:age,:status,:farrowed,:pict,:breedingdate,:forrowingdate)");
		
			  // Bind the parameters
			  $query->bindParam(':name', $pigname, PDO::PARAM_STR);
			  $query->bindParam(':age', $age, PDO::PARAM_STR);
			  $query->bindParam(':status', $status, PDO::PARAM_STR);
			  $query->bindParam(':farrowed', $farrowed, PDO::PARAM_STR);
			  $query->bindParam(':breedingdate', $breedingdate, PDO::PARAM_STR);
			  $query->bindParam(':forrowingdate', $newDates, PDO::PARAM_STR);
			  $query->bindParam(':pict', $filename, PDO::PARAM_STR);

			}
			elseif ($status == "Lactating") {
				 // Prepare the query
				 $query = $dbh->prepare("INSERT INTO tblpigbreeders (name,age, status,img,forrowingdate,piglets,gestateends) VALUES (:name,:age,:status,:pict,:forrowingdate,:pigs,:gestateend)");
		
				 // Bind the parameters
				 $query->bindParam(':name', $pigname, PDO::PARAM_STR);
				 $query->bindParam(':age', $age, PDO::PARAM_STR);
				 $query->bindParam(':status', $status, PDO::PARAM_STR);
				 $query->bindParam(':pigs', $pigs, PDO::PARAM_INT);
				 $query->bindParam(':forrowingdate', $forrowingdate, PDO::PARAM_STR);
				 $query->bindParam(':gestateend', $newDate, PDO::PARAM_STR);
				 $query->bindParam(':pict', $filename, PDO::PARAM_STR);

			}
			else{
				// Prepare the query
				$query = $dbh->prepare("INSERT INTO tblpigbreeders (name,age, status,img) VALUES (:name,:age,:status,:pict)");
		
				// Bind the parameters
				$query->bindParam(':name', $pigname, PDO::PARAM_STR);
				$query->bindParam(':age', $age, PDO::PARAM_STR);
				$query->bindParam(':status', $status, PDO::PARAM_STR);
				$query->bindParam(':pict', $filename, PDO::PARAM_STR);
			}
			  // Execute the query
			  try {
				  $query->execute();
				  if ($query) {
					$success = "Product Added" && header("refresh:1; url=pigbreeders.php");
				  } else {
					$err = "Please Try Again Or Try Later";
				  }
			  } catch (PDOException $ex) {
				  echo $ex->getMessage();
				  exit;
			  }
		  } else {
			  echo "Could not move the uploaded file";
		  }
		} else {
		  echo "File upload error";
		}
		
		  }
	
	?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css">
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

			<ul class="box-info">
			<?php 
$sql ="SELECT id from tblpigbreeders WHERE status= 'Breeding' ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$breeder=$query->rowCount();
?>
				<li class="pigbreeder">
					<i class='bx bx-female-sign' ></i>
					<span class="text">
						<h3><?php echo htmlentities($breeder);?> Sow</h3>
						<p>For Breeding</p>
					</span>
				</li>

				<?php 
$sql ="SELECT id from tblpigbreeders WHERE status= 'Farrowing' ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$farrowing=$query1->rowCount();
?>


				<li class="pigbreeder">
					<i class='bx bxs-baby-carriage' ></i>
					<span class="text">
						<h3><?php echo htmlentities($farrowing);?> Sow</h3>
						<p>Farrowing </p>
					</span>
				</li>

				
				<?php 
$sql ="SELECT id from tblpigbreeders WHERE status= 'Lactating' ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$gestating=$query1->rowCount();
?>

				<li class="pigbreeder">
					<i class='bx bxs-donate-blood' ></i>
					<span class="text">
						<h3><?php echo htmlentities($gestating);?> Sow</h3> 
						<p>Lactating</p>
					</span>
				</li>
			</ul>

			
<div class="table-data">
			<div class="order">
					<div class="heads">
						<h3>Breeders List</h3>
						<div class="search-container">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Search..." id="searchInput" aria-label="Search">
        <div class="input-group-append">
            <span class="input-group-text"><i class='bx bx-search-alt-2'></i></span>
    </div>
</div>
</div>
						<button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmModal"
    class="openModalBtn " ><i class='bx bx-plus-circle'></i> Add New</button>
					</div>

					<ul class="breeders" id="carList">
					<?php 
                          
                          $sql ="SELECT * FROM tblpigbreeders WHERE status != 'Culling'";
                          $query3 = $dbh->prepare($sql);
                          $query3->execute();
                          $results=$query3->fetchAll(PDO::FETCH_OBJ);
                          
                          foreach($results as $result){
                              $date = new DateTime($result->date);
                              $formatteddate = $date->format('F j, Y');

							  $breedingdate = new DateTime($result->breedingstart);
                              $formattedbreed = $breedingdate->format('F j, Y');

							  $forrowingdate = new DateTime($result->forrowingdate);
                              $formattedforrow = $forrowingdate->format('F j, Y');

							  $gestatedate = new DateTime($result->gestateends);
                              $formattedgestate =  $gestatedate->format('F j, Y');

                          
                          ?>
                              
					<li data-make="<?php echo htmlentities($result->name); ?>" data-model="<?php echo htmlentities($result->status); ?>" data-year="<?php echo htmlentities($result->age); ?>">
    <div class="card">
        <div class="image-container">
            <img src="img/<?php echo htmlentities($result->img); ?>" class="card-img-top" alt="pig">
            <div class="image-overlay"></div> 
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlentities($result->name); ?></h5>
			<div class="flex">
			<p class="card-text <?php echo htmlentities($result->status)?>"><?php echo htmlentities($result->status); ?></p>
           
			

			<?php 
    if ($result->status == "Farrowing") {
        // If status is "Forrowing", display breeding start and forrowing date
        echo '<p class="card-text"><span>Farrowing Date:</span> <br>' . htmlentities($formattedforrow) . '</p>';
    } elseif ($result->status == "Lactating") {
        // If status is "Lactating", display piglets and gestate ends
        echo '<p class="card-text"><span>Piglets:</span> ' . htmlentities($result->piglets) . '</p>';
        echo '<p class="card-text"><span>Gestate Ends:</span> <br> ' . htmlentities($formattedgestate) . '</p>';
    }
	
?>
  <p class="card-text"><span>Age:</span> <?php echo htmlentities($result->age); ?></p>
</div>
			<a href="breederdetails.php?id=<?php echo htmlentities($result->id); ?>" class="view-btn">View</a>

		</div>
    </div>
</li>

<?php }?>
</ul>
</div>

</div>	

		<!-- add pig breeder Modal -->

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Breeder</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        
  <div class="col">
  <label for="fullname">Name</label>
    <input type="text" id="fullname" name="name" class="form-control" placeholder="Sow name" aria-label="First name" autocomplete="given-name">
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
                <button type="submit" name="pig" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
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

document.getElementById('statusSelect').addEventListener('change', function() {
    var forrowingFieldsDiv = document.getElementById('forrowingFields');
    var gestatingFieldsDiv = document.getElementById('gestatingFields');

    if (this.value === 'Farrowing') {
        forrowingFieldsDiv.style.display = 'block';  // Show Forrowing fields
        gestatingFieldsDiv.style.display = 'none';   // Hide Lactating fields
    } else if (this.value === 'Lactating') {
        forrowingFieldsDiv.style.display = 'none';   // Hide Forrowing fields
        gestatingFieldsDiv.style.display = 'block';  // Show Lactating fields
    } else {
        forrowingFieldsDiv.style.display = 'none';   // Hide Forrowing fields
        gestatingFieldsDiv.style.display = 'none';   // Hide Lactating fields
    }
});
</script>
	<script src="script.js"></script>
</body>
</html>
<?php } ?>