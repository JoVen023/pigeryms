<?php
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}


else{
	$_SESSION['sidebarname'] ='UnHealthy Piglets';
	
	
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
$sql ="SELECT id from unhealthy_piglets WHERE status= 'Diagnosed' ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$breeder=$query->rowCount();
?>
				<li class="pigbreeder">
					<i class='bx bxs-virus' ></i>
					<span class="text">
						<h3><?php echo htmlentities($breeder);?> Piglets</h3>
						<p>Diagnosed</p>
					</span>
				</li>

				<?php 
$sql ="SELECT id from unhealthy_piglets WHERE status= 'Treatment' ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$farrowing=$query1->rowCount();
?>


				<li class="pigbreeder">
					<i class='bx bxs-capsule' ></i>
					<span class="text">
						<h3><?php echo htmlentities($farrowing);?> Piglets</h3>
						<p>Under Treatment </p>
					</span>
				</li>

				
				<?php 
$sql ="SELECT id from unhealthy_piglets WHERE status= 'Recovered' ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$gestating=$query1->rowCount();
?>

				<li class="pigbreeder">
					<i class='bx bxs-donate-blood' ></i>
					<span class="text">
						<h3><?php echo htmlentities($gestating);?> Piglets</h3> 
						<p>Recovered</p>
					</span>
				</li>

                <?php 
$sql ="SELECT id from unhealthy_piglets WHERE status= 'Deceased' ";
$query1 = $dbh -> prepare($sql);
$query1->execute();
$results=$query1->fetchAll(PDO::FETCH_OBJ);
$gestating=$query1->rowCount();
?>

				<li class="pigbreeder">
					<i class='bx bx-meh-alt'></i></i>
					<span class="text">
						<h3><?php echo htmlentities($gestating);?> Piglets</h3> 
						<p>Deceased</p>
					</span>
				</li>
                
                
			</ul>

			
<div class="table-data">
			<div class="order">
					<div class="heads">
						<h3>UnHealthy Piglets List</h3>
						<div class="search-container">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Search..." id="searchInput" aria-label="Search">
        <div class="input-group-append">
            <span class="input-group-text"><i class='bx bx-search-alt-2'></i></span>
    </div>
</div>
</div>
						<button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmModal"
    class="openModalBtn " hidden><i class='bx bx-plus-circle'></i> Add New</button>
					</div>

					<ul class="breeders" id="carList">
					<?php 
                          
                          $sql ="SELECT up.*,p.name as piglet_name,up.status as piglet_status,p.img as img FROM unhealthy_piglets up  LEFT JOIN  piglets p ON p.id = up.piglet_id WHERE p.status = 'UnHealthy'";
                          $query3 = $dbh->prepare($sql);
                          $query3->execute();
                          $results=$query3->fetchAll(PDO::FETCH_OBJ);
                          
                          foreach($results as $result){
                              $date = new DateTime($result->date);
                              $formatteddate = $date->format('F j, Y');

							

                          
                          ?>
                              
					<li data-make="<?php echo htmlentities($result->piglet_name); ?>" data-model="<?php echo htmlentities($result->piglet_status); ?>" data-year="<?php echo htmlentities($result->details); ?>">
    <div class="card">
        <div class="image-container">
            <img src="img/<?php echo htmlentities($result->img); ?>" class="card-img-top" alt="pig">
            <div class="image-overlay"></div> 
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlentities($result->piglet_name); ?></h5>
			<div class="flex">
			<p class="card-text <?php echo htmlentities($result->piglet_status)?>"><?php echo htmlentities($result->piglet_status); ?></p>

				<p class="card-text"><?php echo htmlentities($result->details); ?></p>
           
			

			<?php 
    if ($result->piglet_status == "Diagnosed") {
        echo '<p class="card-text"><span>Diagnosed Date:</span> <br>' . htmlentities($formatteddate) . '</p>';
    } elseif ($result->piglet_status == "Treatment") {
        echo '<p class="card-text"><span>Treatment Date:</span> <br> ' . htmlentities($formatteddate) . '</p>';
    } elseif ($result->piglet_status == "Recovery Date") {
        echo '<p class="card-text"><span>Recovery Date:</span> <br> ' . htmlentities($formatteddate) . '</p>';
    }else{
        echo '<p class="card-text"><span>Deceased Date:</span> <br> ' . htmlentities($formatteddate) . '</p>';
	}
	
?>
</div>
			<a href="unhealthypigletdetails.php?id=<?php echo htmlentities($result->id); ?>" class="view-btn" disable>View</a>

		</div>
    </div>
</li>

<?php }?>
</ul>
</div>

</div>	

		<!-- add pig breeder Modal

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Breeder</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="" method="POST" enctype="multipart/form-data">
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
        Fields for Forrowing
        <div id="forrowingFields" style="display: none;">
		
            <label for="breedingDate" class="me-1">Breeding Date:</label>
            <input type="date" name="breedingdate" id="breedingDate" class="me-5">
            
           
        </div>

        Fields for Lactating
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

				</div> -->

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