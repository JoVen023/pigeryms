<?php
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
		
header('location:index.php');
}
else{
  $_SESSION['sidebarname'] = 'Cull';
if(isset($_POST['add'])){
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
        }
          // Execute the query
          try {
              $query->execute();
              echo "<script type='text/javascript'>alert('Added Successfully'); window.location.href = 'culling.php';</script>";
          } catch (PDOException $ex) {
              echo $ex->getMessage();
              exit;
          }
      }
    }

    if(isset($_POST['amount'])){
      $id = intval($_POST['sow_id']); 
      $total = $_POST['total'];
  
      try {
  
  
  $query1 = $dbh->prepare("UPDATE tblculling SET amount = :amount WHERE id=:id");
  $query1->bindParam(':id', $id, PDO::PARAM_INT);
  $query1->bindParam(':amount', $total, PDO::PARAM_INT);
  $query1->execute();

  
  try {
      $query1->execute();
    
  } catch(PDOException $e) {
      echo "Query failed: " . $e->getMessage();
      exit;
  }

      // Execute the query
      echo "<script>
      alert('Updated successfully!');
      window.location.href = 'culling.php?msg=success';
    </script>";
  } catch (PDOException $ex) {
      error_log($ex->getMessage());
      header("Location: culling.php?msg=error");
      exit;
      } 
  
  }



    if(isset($_POST['move'])){
      $id = intval($_POST['sow_id']); 
      $custname = $_POST['custname'];
      $date = $_POST['date'];
      $total = $_POST['total'];
  
      try {
  
      $query = $dbh->prepare("INSERT INTO tblsoworder(sow_id, custname, date, totalamount)VALUES(:id, :name,:date,:total)");
  
  // Bind all parameters
  $query->bindParam(':id', $id, PDO::PARAM_INT);
  $query->bindParam(':name', $custname, PDO::PARAM_STR);
  $query->bindParam(':date', $date, PDO::PARAM_STR);
  $query->bindParam(':total', $total, PDO::PARAM_STR);
  $query->execute();
  
  $query1 = $dbh->prepare("UPDATE tblculling SET status = 'Purchased' WHERE id=:id");
  $query1->bindParam(':id', $id, PDO::PARAM_INT);
  $query1->execute();

  $sql = "UPDATE tblsales SET total_sales = total_sales + :total"  ;
  $query2 = $dbh->prepare($sql);
  $query2->bindParam(':total', $total, PDO::PARAM_INT);
  
  try {
      $query2->execute();
    
  } catch(PDOException $e) {
      echo "Query failed: " . $e->getMessage();
      exit;
  }

      // Execute the query
      echo "<script>
      alert('Moved successfully!');
      window.location.href = 'sales.php?msg=success';
    </script>";
  } catch (PDOException $ex) {
      error_log($ex->getMessage());
      header("Location: sales.php?msg=error");
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
	<title>Culling</title>
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
						<h3>Sow List</h3>
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
                          
                          $sql ="SELECT * FROM tblculling WHERE status = 'Culling'";
                          $query3 = $dbh->prepare($sql);
                          $query3->execute();
                          $results=$query3->fetchAll(PDO::FETCH_OBJ);
                          foreach($results as $result){
                          
                          ?>
                              
					<li data-make="<?php echo htmlentities($result->name); ?>" data-model="<?php echo htmlentities($result->status); ?>" data-year="<?php echo htmlentities($result->age); ?>">
    <div class="card cull">

        <div class="image-container">
            <img src="img/<?php echo htmlentities($result->img); ?>" class="card-img-top" alt="...">
          
            <div class="image-overlay"></div> 
        </div>
        <div class="card-body cull">
          <div class="d-flex justify-content-between align-items-center">
          <div class="flex-grow-1 text-end p-0">
    <h5 class="card-title ps-4"><?php echo htmlentities($result->name); ?></h5>
</div>
<button type="button" class="btn btn-sm delete-btn ms-auto p-0" title="Delete Pig"  data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo htmlentities($result->id); ?>"><i class='bx bx-trash bx-sm text-danger'></i></button>

        </button></span></h5> 
        </div>
           
            <div class="flex">
			<p class="card-text <?php echo htmlentities($result->status); ?>"><?php echo htmlentities($result->status); ?></p>
      <p class="card-text"><span>Price:</span><br>₱<?php echo htmlentities($result->amount);?></p>
            <p class="card-text"><span>Age:</span><br><?php echo htmlentities($result->age);?></p>
            <button type="button" title="Update Amount" data-bs-toggle="modal" data-bs-target="#amountModal" class="moveModalBtn mb-1">
    <i class='bx bx-up-arrow-circle mb-1'></i>Update Amount
</button>
            <button type="button" title="Already Purchased" data-bs-toggle="modal" data-bs-target="#moveModal" class="moveModalBtn">
    <i class='bx bx-up-arrow-circle'></i>Purchased
</button>

        </div>

		</div>
    </div>
</li>


<!-- moveculling sow modal -->

<div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Sow Purchased Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
      <div class="row">
      <input type="hidden" name="sow_id" class="form-control" placeholder="Pig name" aria-label="name" value="<?php echo htmlentities($result->id);?>">
  <div class="col">
  <label for="fullnames">Customer Name</label>
    <input type="text" id="fullnames" name="custname" class="form-control" placeholder="Fullname" aria-label="name" required>
  </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="orderdate">Date Purchased</label>
          <input type="date" id="orderdate" name="date" class="form-control" placeholder="Month" aria-label="Month" required>
        </div>
</div>
<br>

      <div class="row">
      <div class="col">
                                 <label for="total">Total Amount</label>
  									<input type="number" id="total" name="total" class="form-control form-control-sm rounded-0" required>
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
<!-- move cull modal -->


<!-- amount sow modal -->

<div class="modal fade" id="amountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Sow Purchased Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
      <div class="row">
      <input type="hidden" name="sow_id" class="form-control" placeholder="Pig name" aria-label="name" value="<?php echo htmlentities($result->id);?>">
      <div class="col">
                                 <label for="total">Total Amount</label>
  									<input type="number" id="total" name="total" class="form-control form-control-sm rounded-0" required>
								</div>
</div>
<br>

      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="amount" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>    
<!-- amount  cull modal -->


<!-- deletepig  Modal -->
<div class="modal fade" id="deleteModal-<?php echo htmlentities($result->id); ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo htmlentities($result->id); ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/deletepig.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this Cull?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deletepig('<?php echo htmlentities($result->id); ?>')" name="delete">Confirm</button>
                </div>
            </div>
        </div>
    </div>

<!-- delete pig Modal -->


<?php }?>
</ul>
</div>
	
</div>	

<!-- add culling sow modal -->

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Sow For Culling</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
  <div class="col">
  <label for="sowname">Sow Name</label>
    <input type="text" name="name" id="sowname" class="form-control" placeholder="Pig name" aria-label="name" autocomplete="none" required>
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
                <button type="submit" name="add" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>    
<!-- add cull modal -->



		</main>
		<!-- MAIN -->
			<!-- FOOTER -->
		<?php include('includes/footer.php');?>
		<!-- FOOTER -->
	</section>
	<!-- CONTENT -->
	
<script>

function deletepig(id) {
    $.ajax({
        url: 'delete.php',  
        type: 'POST',
        data: { cull_id: id },
        success: function(response) {
            $('#deleteModal-' + id).modal('hide');
            alert('Deleted Succesfully');
            location.reload();
        },
        error: function() {
            alert('An error occurred while trying to delete the pig.');
        }
    });
}

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