<?php

error_reporting(0);
include('includes/config.php');
include 'fetchsow.php';
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
  $_SESSION['sidebarname'] = 'Inventory';
  $sow = getMenutype($dbh);

  if(isset($_POST['feed'])){
    $feedname=$_POST['feedname'];
    $quantity=$_POST['quantitys'];
    $pruchased=$_POST['datepurchased'];
    $price=$_POST['feedprice'];
    $consume=$_POST['dateconsume'];

          // Prepare the query
          $query = $dbh->prepare("INSERT INTO tblfeeds (feedsname,quantity, price, datepurchased,consumedate) VALUES (:feedname,:quantity,:price,:datepurchased,:consume)");
          // Bind the parameters
          $query->bindParam(':feedname', $feedname, PDO::PARAM_STR);
          $query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
          $query->bindParam(':price', $price, PDO::PARAM_INT);
          $query->bindParam(':datepurchased', $pruchased, PDO::PARAM_STR);
          $query->bindParam(':consume', $consume, PDO::PARAM_STR);
          // Execute the query
          try {
              $query->execute();
              echo "<script type='text/javascript'>alert('Added Successfully'); window.location.href = 'inventory.php';</script>";
          } catch (PDOException $ex) {
              echo $ex->getMessage();
              exit;
          }
      }


  if(isset($_POST['pig'])){
$pigname=$_POST['name'];
$month=$_POST['age'];
$age = $month . " Months";
$weightclass=$_POST['weightclass'];
$sow=$_POST['sow'];
$pigletsid=$_POST['piglet'];
$price=$_POST['price'];

$genderquery = $dbh -> prepare("SELECT gender from piglets WHERE id = :pigletsid");
$genderquery->bindParam(':pigletsid',$pigletsid,PDO::PARAM_INT);
$genderquery->execute();
$gender = $genderquery->fetch(PDO::FETCH_ASSOC);

$piggender = $gender['gender'];




function handleImageUpload($imageKey) {
  if ($_FILES[$imageKey]['error'] == UPLOAD_ERR_OK) {
      $filename =basename($_FILES[$imageKey]['name']);
      $uploadPath = 'img/' . $filename;
      if (move_uploaded_file($_FILES[$imageKey]['tmp_name'], $uploadPath)) {
          return $filename;
      } else {
          return false;
      }
  }
  return false;
}

  $imgMain = handleImageUpload('pict');
  $imgBack = handleImageUpload('pictback');
  $imgSide = handleImageUpload('pictside');
  $imgFront = handleImageUpload('pictfront');
  // Move the uploaded file to the desired directory
  if($imgMain && $imgBack && $imgSide && $imgFront){
   
      // Prepare the query
      $query = $dbh->prepare("INSERT INTO tblpigforsale (name, sow_id,piglet_id, sex, age, weight_class, price, img, back, side, front) VALUES (:name, :sow, :pigletid,:sex, :age, :weightclass, :price, :imgMain, :imgBack, :imgSide, :imgFront)");

      // Bind the parameters
      $query->bindParam(':name', $pigname, PDO::PARAM_STR);
      $query->bindParam(':sow', $sow, PDO::PARAM_INT);
      $query->bindParam(':pigletid', $pigletsid, PDO::PARAM_INT);
      $query->bindParam(':sex', $piggender, PDO::PARAM_STR);
      $query->bindParam(':age', $age, PDO::PARAM_STR);
      $query->bindParam(':weightclass', $weightclass, PDO::PARAM_STR);
      $query->bindParam(':price', $price, PDO::PARAM_INT);
      $query->bindParam(':imgMain', $imgMain, PDO::PARAM_STR);
      $query->bindParam(':imgBack', $imgBack, PDO::PARAM_STR);
      $query->bindParam(':imgSide', $imgSide, PDO::PARAM_STR);
      $query->bindParam(':imgFront', $imgFront, PDO::PARAM_STR);

      $query5 =  $dbh->prepare("UPDATE piglets SET status = 'Posted',move = 1  WHERE id = :pigletsid");
      $query5 ->bindParam(':pigletsid',$pigletsid,PDO::PARAM_INT);
      // Execute the query
      try {
        $query5->execute();
          $query->execute();
          echo "<script type='text/javascript'>alert('Added Successfully'); window.location.href = 'inventory.php';</script>";
      } catch (PDOException $ex) {
          echo $ex->getMessage();
          exit;
      }
  } else {
      echo "Could not move the uploaded file";
  }


  }




  if(isset($_POST['update'])){
    $id=$_POST['id'];
    $pigname=$_POST['name'];
    $sex=$_POST['sex'];
    $age = $_POST['month'];
    $weightclass=$_POST['weightclass'];
    $price=$_POST['price'];

    // Initially set the filename as null
    function handleImageUpload($imageKey, $existingImage) {
      if ($_FILES[$imageKey]['error'] == UPLOAD_ERR_OK) {
          $filename = basename($_FILES[$imageKey]['name']);
          $uploadPath = 'img/' . $filename;
          if (move_uploaded_file($_FILES[$imageKey]['tmp_name'], $uploadPath)) {
              return $filename;
          }
      }
      return $existingImage;  // If no new upload, return existing filename
  }

  // Fetch current data from database
  $fetchQuery = $dbh->prepare("SELECT * FROM tblpigforsale WHERE id = :id");
  $fetchQuery->bindParam(':id', $id, PDO::PARAM_STR);
  $fetchQuery->execute();
  $currentData = $fetchQuery->fetch(PDO::FETCH_OBJ);

  // Handle uploads for each image
  $imgMain = handleImageUpload('pict', $currentData->img);
  $imgBack = handleImageUpload('back', $currentData->back);
  $imgSide = handleImageUpload('side', $currentData->side);
  $imgFront = handleImageUpload('front', $currentData->front);

  // Prepare the update query
  $query = $dbh->prepare("UPDATE tblpigforsale SET name=:name, sex=:sex, age=:age, weight_class=:weightclass, price=:price, img=:imgMain, back=:imgBack, side=:imgSide, front=:imgFront WHERE id = :id");

    // Bind the parameters
    $query->bindParam(':name', $pigname, PDO::PARAM_STR);
    $query->bindParam(':sex', $sex, PDO::PARAM_STR);
    $query->bindParam(':age', $age, PDO::PARAM_STR);
    $query->bindParam(':weightclass', $weightclass, PDO::PARAM_STR);
    $query->bindParam(':price', $price, PDO::PARAM_INT);
    $query->bindParam(':id', $id, PDO::PARAM_STR);
    $query->bindParam(':imgMain', $imgMain, PDO::PARAM_STR);
    $query->bindParam(':imgBack', $imgBack, PDO::PARAM_STR);
    $query->bindParam(':imgSide', $imgSide, PDO::PARAM_STR);
    $query->bindParam(':imgFront', $imgFront, PDO::PARAM_STR);

    // Execute the query
    try {
        $query->execute();
        echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'inventory.php';</script>";
    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    }
}
    

if(isset($_POST['updatefeed'])){
  $id=$_POST['id'];
  $name=$_POST['names'];
  $quantity=$_POST['quantitys'];
  $price = $_POST['prices'];
  $date =$_POST['dates'];
  $consumedate =$_POST['consumedate'];
  // Fetch current data from database
  $fetchQuery = $dbh->prepare("SELECT * FROM tblfeeds WHERE id = :id");
  $fetchQuery->bindParam(':id', $id, PDO::PARAM_STR);
  $fetchQuery->execute();
  $currentData = $fetchQuery->fetch(PDO::FETCH_OBJ);
  // Prepare the query
  $query = $dbh->prepare("UPDATE tblfeeds SET feedsname=:name, quantity=:quantity, price=:price, datepurchased=:date,consumedate=:consumedate  WHERE id = :id");

  // Bind the parameters
  $query->bindParam(':name', $name, PDO::PARAM_STR);
  $query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $query->bindParam(':price', $price, PDO::PARAM_INT);
  $query->bindParam(':date', $date, PDO::PARAM_STR);
  $query->bindParam(':consumedate', $consumedate, PDO::PARAM_STR);
  $query->bindParam(':id', $id, PDO::PARAM_STR);

  // Execute the query
  try {
      $query->execute();
      echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'inventory.php';</script>";
  } catch (PDOException $ex) {
      echo $ex->getMessage();
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
	<title>Inventory</title>
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
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

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
        <?php
// Assuming you have a 'id' and 'sowname' column in your tblgrowingphase table
$sqlGroups = "SELECT DISTINCT id, sowname FROM tblgrowingphase WHERE status = 'grower'";
$queryGroups = $dbh->prepare($sqlGroups);
$queryGroups->execute();
$availableGroups = $queryGroups->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="left">
    <h1>Pigs List</h1>
    <div class="filter-group">
        <label for="groupFilter">Filter by Group:</label>
        <select id="groupFilter">
            <option value="all">All Pigs</option>
            <?php
            foreach ($availableGroups as $group) {
                $value = $group['sowname'];
                echo "<option value='$value'>$group[sowname]</option>";
            }
            ?>
        </select>
    </div>

                    <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmModal"
    class="openModalBtn" ><i class='bx bx-plus-circle'></i> Add New</button>
				</div>
                <table id="myTable">
						<thead>
							<tr>  
                                <th>ID</th>
								<th>Name</th>
                                <th>Sex</th>
								<th>Age</th>
                                <th>Weight Class</th>
                                <th>Price</th>
                                <th>Group</th>
                                <th>Creation Date</th>  
                                <th>Action</th>
                                
							</tr>
						</thead>
                        
						<tbody>
                        <?php 
                          
                          $sql = "SELECT pf.*, gp.sowname AS sowname
                          FROM tblpigforsale pf
                          LEFT JOIN tblgrowingphase gp ON pf.sow_id = gp.id
                          WHERE pf.status IS NULL OR pf.status = ''";
                          $query3 = $dbh->prepare($sql);
                          $query3->execute();
                          $results=$query3->fetchAll(PDO::FETCH_OBJ);
                          
                          foreach($results as $result){
                              $date = new DateTime($result->CreationDate);
                              $formatteddate = $date->format('F j, Y');
                          
                          ?>
                              
                              <tr>
	<td>
	<p><?php echo htmlentities($result->id); ?></p>
		</td>

	<td><?php echo htmlentities($result->name); ?></td>
	<td><?php echo htmlentities($result->sex); ?></td>
    <td><?php echo htmlentities($result->age); ?></td>
    <td><?php echo htmlentities($result->weight_class);?></td>
    <td><span>&#8369;</span><?php echo htmlentities($result->price); ?>/kg</td>
    <td><?php echo htmlentities($result->sowname); ?></td>
    <td><?php echo htmlentities($formatteddate); ?></td>
    <!-- Button trigger modal -->
    <td class="action">
      <button type="button" class="btn delete" title="Delete Pig"  data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo htmlentities($result->id); ?>"><i class='bx bx-trash'></i></button>
    <button type="button" class="btn btn-sm updateModalBtn" title="Update Pig" data-bs-toggle="modal" data-bs-target="#updateModal" data-pigid="<?php echo $result->id; ?>"><i class='bx bx-edit'></i></button>
                          </td>
    <!-- Button trigger modal -->
  </tr>
  
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
                    <h3 class="confirm">Are you sure you want to delete this pig?</h3>
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
<!-- update pig Modal -->

<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Pig</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForm" action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
          
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="update" class="btn btn-primary">Update</button>
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
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Pig</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        
  <div class="col">
  <label for="name">Name</label>
    <input type="text" name="name" id="name" class="form-control" placeholder="Pig name" aria-label="First name" autocomplete="given-name" required>
  </div>
  <div class="col">
        <label for="price">Price/kg</label>
          <input type="number"  id="price" name="price"class="form-control" placeholder="Price" aria-label="Price" required>
        </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="month">Age(Month)</label>
          <input type="number" id="month" name="age"class="form-control" placeholder="Month" aria-label="Month" required>
        </div>
        <div class="col">
        <label for="weight">Weight Class</label>
  <select name="weightclass" id="weight" class="form-select form-select-sm" aria-label="weightclass" required>
  <option selected>Select</option>
  <option value="30-40kg">30-40kg</option>
  <option value="40-50kg">40-50kg</option>
  <option value="50-60kg">50-60kg</option>
</select>
        </div>
       
      </div>
      <br>
      <div class ="row">
      <div class="col">
        
      <label for="sow">Pig Group</label>
      <select
              id="parentSelect"
              name="sow"
              class="form-select form-select-sm"
              required="required"
              onchange="updateChildSelect()">
            
              <?php echo $sow; ?>
            </select>
        </div>
        <div class="col">
  <label for="sex">Piglets</label>
  <select name="piglet"  class="form-select form-select-sm" id="childSelect">
  <option value="">Select Piglet</option>

</select>
  </div>



</div>
      <br>
      <div class="row">
      <div class="col">
                                 <label for="map">Main Picture</label>
  									<input type="file" id="map" name="pict" class="form-control form-control-sm rounded-0" required>
								</div>
                <div class="col">
                                 <label for="map">Back Angle</label>
  									<input type="file" id="map" name="pictback" class="form-control form-control-sm rounded-0" required>
								</div>
                <div class="col">
                                 <label for="map">Side Angle</label>
  									<input type="file" id="map" name="pictside" class="form-control form-control-sm rounded-0" required>
								</div>
                <div class="col">
                                 <label for="map">Front Angle</label>
  									<input type="file" id="map" name="pictfront" class="form-control form-control-sm rounded-0" required>
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
        </div>

        	
        </div>



<!-- add pig Modal -->

  
        <div class="table-data">
				<div class="order">
				<div class="left">
					<h1>Feeds List</h1>
                    <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#confirmfeedModal"
    class="openModalBtn" ><i class='bx bx-plus-circle'></i> Add New</button>

   
				</div>
        
                <table id="mysecondTable">
						<thead>
							<tr>
                                <th>ID</th>
						          		      <th>Feeds Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Purchased Date</th>  
                                <th>Consumed Date</th>  
                                <th>Action</th>
                                
							</tr>
						</thead>
                        
						<tbody>
                        <?php 
                          
                          $sql ="SELECT * FROM tblfeeds";
                          $query4 = $dbh->prepare($sql);
                          $query4->execute();
                          $results=$query4->fetchAll(PDO::FETCH_OBJ);
                          
                          foreach($results as $res){
                              $dates = new DateTime($res->datepurchased);
                              $consumeddate = new DateTime($res->consumedate);
                              $formatcdate = $consumeddate->format('F j, Y');
                              $formatdate = $dates->format('F j, Y');
                          
                          ?>
                              
                              <tr>
	<td>
	<p><?php echo htmlentities($res->id); ?></p>
		</td>
	<td><?php echo htmlentities($res->feedsname); ?></td>
	<td><?php echo htmlentities($res->quantity); ?></td>
    <td><?php echo htmlentities($res->price); ?></td>
    <td><?php echo htmlentities($formatdate); ?></td>
    <td><?php echo htmlentities($formatcdate); ?></td>
    <!-- Button trigger modal -->
    <td class="action">
      <button type="button" class="btn delete" title="Delete Feed"  data-bs-toggle="modal" data-bs-target="#deletefeedModal-<?php echo htmlentities($res->id); ?>"><i class='bx bx-trash'></i></button>
    <button type="button" class="btn updatefeed" title="Update Feeds" data-bs-toggle="modal" data-bs-target="#updatefeedModal" data-feedid="<?php echo $res->id; ?>"><i class='bx bx-edit'></i></button>
                          </td>
    <!-- Button trigger modal -->
  </tr>
  
<!-- deletefeed  Modal -->
<div class="modal fade" id="deletefeedModal-<?php echo htmlentities($res->id); ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo htmlentities($res->id); ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/delfeeds.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this feed?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deletefeed('<?php echo htmlentities($res->id); ?>')" name="deletefeed">Confirm</button>
                </div>
            </div>
        </div>
    </div>

<!-- delete feed Modal -->

<!-- update feed Modal -->

<div class="modal fade" id="updatefeedModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Feed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updatefedForm" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="updatefeed" class="btn btn-primary">Update</button>
        </form>
      </div>
  
    </div>
  </div>
</div>                 
<!-- update feed Modal -->



<?php 
} 
?>	
						</tbody>
					</table>
 <!-- add feed Modal -->
 <div class="modal fade" id="confirmfeedModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Feed</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form  action="<?=$_SERVER['PHP_SELF']?>" method="POST">
      <div class="row">
  <div class="col">
  <label for="feedname">Feeds Name</label>
    <input type="text" name="feedname" id="feedname" class="form-control" placeholder="Feeds name" aria-label="Feed name" autocomplete="given-name" required>
  </div>
</div>
      <br>
      <div class ="row">
      <div class="col">
        <label for="quantitys">Quantity</label>
          <input type="number"  id="quantitys" name="quantitys"class="form-control" placeholder="Quantity" aria-label="Quantity" required>
        </div>
        <div class="col">
        <label for="price">Price/kg</label>
          <input type="number"  id="feedprice" name="feedprice"class="form-control" placeholder="Price" aria-label="Price" required>
        </div>

</div>
      <br>
      <div class="row">
      <label for="datepurchased">Date Purchased :</label>
          <input type="date"  id="datepurchased" name="datepurchased" class="form-control" placeholder="datepurchased" aria-label="datepurchased" required>

</div>
<br>
<div class="row">
      <label for="dateconsumed">Consumed Date :</label>
          <input type="date"  id="dateconsumed" name="dateconsume" class="form-control" placeholder="dateconsumed" aria-label="dateconsumed" required>

</div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="feed" class="btn btn-primary" id="confirmBtn">Confirm</button>
      </div>
      </form>
    </div>
  </div>
                </div>

				</div>
        </div>

        		<!-- add feed Modal -->

					
        </div>



<!-- add pig Modal -->
</main>
		
		<!-- MAIN -->
			<!-- FOOTER -->
		<?php include('includes/footer.php');?>
		<!-- FOOTER -->
	</section>
	<!-- CONTENT -->

 

	<script>
$(document).ready(function () {
    // Initialize DataTable
    $('#myTable').DataTable();

    $('#mysecondTable').DataTable();
    // Handle delete button clicks

    var dataTable = $('#myTable').DataTable();

// Listen for changes in the filter dropdown
$('#groupFilter').on('change', function() {
    var selectedValue = $(this).val();

    // Update the DataTable based on the selected group
    if (selectedValue === 'all') {
        // Show all rows if 'All Pigs' is selected
        dataTable.search('').draw();
    } else {
        // Filter rows based on the selected group_id
        dataTable.column(6)  // Assuming 'Group' column is at index 6
            .search(selectedValue)
            .draw();
    }
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


    $(document).on('click', '.delete', function() {
        // Save the pig ID to deletePigId
        deletefeedId = $(this).data('id');
        // Show the modal
        $('#deletefeedModal-' + deletefeedId).modal('show');
    });

    // Handle the "Confirm" button click
    $(document).on('click', '#confirmDelete', function() {
        // Call deletepig
        deletefeed(deletefeedId);
    });


});

function updateChildSelect() {
    var parentSelect = document.getElementById('parentSelect');
    var childSelect = document.getElementById('childSelect');
    var selectedParentId = parentSelect.value;

    childSelect.innerHTML = '<option value="">Select Corresponding Pigs</option>';

    if (selectedParentId) {
      fetch('getChildOptions.php?sowid=' + selectedParentId)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();  
    })
    .then(data => {
        if (Array.isArray(data)) {  
            data.forEach(function(child) {
                var option = document.createElement('option');
                option.value = child.id;
                option.text = child.name;
                childSelect.appendChild(option);
            });
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

function deletefeed(id) {
    // Send a POST request to delete.php
    $.ajax({
        url: 'delete.php',  // This sends the request to delete.php
        type: 'POST',
        data: { feedid: id },
        success: function(response) {
            // Close the modal
            $('#deletefeedModal-' + id).modal('hide');
            // Reload the page to update the table
            location.reload();
        },
        error: function() {
            alert('An error occurred while trying to delete the feed.');
        }
    });
}




function deletepig(id) {
    // Send a POST request to delete.php
    $.ajax({
        url: 'delete.php',  // This sends the request to delete.php
        type: 'POST',
        data: { id: id },
        success: function(response) {
            // Close the modal
            $('#deleteModal-' + id).modal('hide');
            // Reload the page to update the table
            location.reload();
        },
        error: function() {
            alert('An error occurred while trying to delete the pig.');
        }
    });
}




    $(document).on("click", ".updateModalBtn", function() {
  var pigId = $(this).attr("data-pigid");  // Extract pig ID from data-* attribute
  
  $.ajax({
    url: 'getpigdata.php',  // Endpoint where the server-side code resides
    type: 'POST',
    data: { pigId: pigId},  // Send pig ID to server
    dataType: 'json',  // Expect JSON response from server
    success: function(response) {
      // On successful response, populate the form fields
      // assuming response has properties id, name, sex, age, weight_class, price and img
      $("#updateForm").html(`
     
      <div class="row">
      <input type="hidden" name="id" value="${response.id}">
  <div class="col">
  <label for="fullname">Name</label>
    <input type="text" name="name" class="form-control" value="${response.name}">
  </div>
  <div class="col">
  <label for="fullname">Sex</label>
  <select name="sex" class="form-select form-select-sm" aria-label="Large select example">
  <option selected>${response.sex}</option>
  <option value="Male">Male</option>
  <option value="Female">Female</option>
</select>
  </div>
</div>
<br>
<div class="row">
        
        <div class="col">
        <label for="fullname">Age(Month)</label>
          <input type="text" name="month" class="form-control"value="${response.age}">
        </div>
        <div class="col">
        <label for="fullname">Weight Class</label>
  <select name="weightclass" class="form-select form-select-sm" aria-label="weightclass">
  <option selected>${response.weight_class}</option>
  <option value="30-40kg">30-40kg</option>
  <option value="40-50kg">40-50kg</option>
  <option value="50-60kg">50-60kg</option>
</select>
        </div>
        <div class="col">
        <label for="fullname">Price/kg</label>
          <input type="number" name="price" class="form-control" value="${response.price}">
        </div>
      </div>
      <br>
      <div class="row">
      <div class="col">
                                 <label>Add Main Image:</label></label>
  									<input type="file" id="m" name="pict" class="form-control form-control-sm rounded-0">
                    <br>
                    <img src="img/${response.img}" class="rounded mx-auto d-block" alt="pig" width="150px" height="100px">
								</div>
                <div class="col">
                                 <label>Add Back Image:</label></label>
  									<input type="file" id="ma" name="back" class="form-control form-control-sm rounded-0">
                    <br>
                    <img src="img/${response.back}" class="rounded mx-auto d-block" alt="pig" width="150px" height="100px">
								</div>
                <div class="col">
                                 <label>Add Side Image:</label></label>
  									<input type="file" id="map" name="side" class="form-control form-control-sm rounded-0">
                    <br>
                    <img src="img/${response.side}" class="rounded mx-auto d-block" alt="pig" width="150px" height="100px">
								</div>
                <div class="col">
                                 <label>Add Front Image:</label></label>
  									<input type="file" id="maps" name="front" class="form-control form-control-sm rounded-0">
                    <br>
                    <img src="img/${response.front}" class="rounded mx-auto d-block" alt="pig" width="150px" height="100px">
								</div>
</div>

     
      
        
      `);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);  // Log any error to console
    }
    
  });

});


$(document).on("click", ".updatefeed", function() {
  var feedId = $(this).attr("data-feedid");  // Extract pig ID from data-* attribute
  
  $.ajax({
    url: 'getfeed.php',  // Endpoint where the server-side code resides
    type: 'POST',
    data: { feedId: feedId },  // Send pig ID to server
    dataType: 'json',  // Expect JSON response from server
    success: function(response) {
      console.log(response);
      // On successful response, populate the form fields
      // assuming response has properties id, name, sex, age, weight_class, price and img
      $("#updatefedForm").html(`
      <div class="row">
      <input type="hidden" name="id" value="${response.id}">
  <div class="col">
  <label for="fullname">Feeds Name</label>
    <input type="text" name="names" class="form-control" value="${response.name}">
  </div>
  </div>
  <br>
  <div class="row">
  <div class="col">
  <label for="quant">Quantity</label>
    <input type="number" name="quantitys" class="form-control" value="${response.quantity}">
  </div>
  <div class="col">
        <label for="fullname">Total Price</label>
          <input type="number" name="prices" class="form-control"value="${response.price}">
        </div>
</div>
<br>
<div class="row">
        <div class="col">
        <label for="fullname">Purchased Date</label>
          <input type="date" name="dates" class="form-control" value="${response.date}">
        </div>
        <div class="col">
        <label for="fullname">Consumed Date</label>
          <input type="date" name="consumedate" class="form-control" value="${response.consumedate}">
        </div>
      </div>
        
      `);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);  // Log any error to console
    }
  });

});

</script>
	<script src="script.js"></script>
</body>
</html>
<?php } ?>