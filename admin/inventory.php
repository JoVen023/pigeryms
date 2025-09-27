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
  $pigletgroup = getPigletgroup($dbh);

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


      
           // Update Piglets

     if(isset($_POST['updatepiglets'])){
      $id=$_POST['id'];
      $main_id=$_POST['main_id'];
      $groupname=$_POST['groupname'];
      $gender=$_POST['gender'];
      $piglet_weight = $_POST['piglet_weight'];
      $farrow=$_POST['farrow'];
      $price=$_POST['price'];
  
      function handleImageUpload($imageKey, $existingImage) {
        if ($_FILES[$imageKey]['error'] == UPLOAD_ERR_OK) {
            $filename = basename($_FILES[$imageKey]['name']);
            $uploadPath = 'img/img_piglets_for_sale/' . $filename;
            if (move_uploaded_file($_FILES[$imageKey]['tmp_name'], $uploadPath)) {
                return $filename;
            }
        }
        return $existingImage;  // If no new upload, return existing filename
    }
  
    $fetchQuery = $dbh->prepare("SELECT * FROM tblpiglet_for_sale_details WHERE id = :id");
    $fetchQuery->bindParam(':id', $id, PDO::PARAM_STR);
    $fetchQuery->execute();
    $currentData = $fetchQuery->fetch(PDO::FETCH_OBJ);  
  
    $imgMain = handleImageUpload('img', $currentData->img);
  
    $query3 = $dbh->prepare("UPDATE tblpiglet_for_sale SET Farrowed_Date=:farrow WHERE id = :main_id");
      // $query3->bindParam(':groupname', $groupname, PDO::PARAM_STR);
      $query3->bindParam(':farrow', $farrow, PDO::PARAM_STR);
      $query3->bindParam(':main_id', $main_id, PDO::PARAM_STR);

      $query4 = $dbh->prepare("UPDATE tblpiglet_for_sale_details SET  gender=:gender,name=:name, piglet_weight=:piglet_weight, price=:price, img=:imgMain WHERE id = :id");
      $query4->bindParam(':gender', $gender, PDO::PARAM_STR);
      $query4->bindParam(':name', $groupname, PDO::PARAM_STR);
      $query4->bindParam(':price', $price, PDO::PARAM_INT);
      $query4->bindParam(':piglet_weight', $piglet_weight, PDO::PARAM_STR);
      $query4->bindParam(':id', $id, PDO::PARAM_STR); 
      $query4->bindParam(':imgMain', $imgMain, PDO::PARAM_STR);
  
      try {
          $query4->execute();
          $query3->execute();
          echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'inventory.php';</script>";
      } catch (PDOException $ex) {
          echo $ex->getMessage();
          exit;
      }
     }

           // Update Piglets





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
          if($query && $query5){
            $success = "Updated Successfully" && header("url=inventory.php");
  
          }else{
            $error = "Please try again Later";
          }

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
        if($query){
          $success = "Updated Successfully" && header("refresh:1; url=inventory.php");

        }else{
          $error = "Please try again Later";
        }

        // echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'inventory.php';</script>";
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
  

if (isset($_POST['sellpiglets'])) {
  $growingphase_id = $_POST['sow'];
    $name = $_POST['sow'];
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
              window.location.href='inventory.php';</script>";
    } catch (PDOException $e) {
        header("Location: inventory.php");
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
        <?php
// Assuming you have a 'id' and 'sowname' column in your tblgrowingphase table
$sqlGroups = "SELECT DISTINCT id, name FROM tblpigforsale WHERE status NOT IN('ordered')";
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
      <div class ="row">
      <div class="col">
        
      <label for="parentSelect">Pig Group</label>
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
  <label for="childSelect">Piglets</label>
  <select name="piglet"  
  class="form-select form-select-sm"
   id="childSelect"
   required="required"
   onchange="updatpigletSelect()">
  <option value="">Select Piglet</option>

</select>
  </div>



</div>
<br>
      <div class="row">
        
  <!-- <div class="col">
  <label for="name">Name</label>
  </div> -->
  <div class="col">
        <label for="month">Age(Month)</label>
          <input type="number" id="month" name="age" class="form-control" placeholder="Month" aria-label="Month" readonly>
        </div>
  <div class="col">
        <label for="price">Price/kg</label>
          <input type="number"  id="price" name="price"class="form-control" placeholder="Price" aria-label="Price" required>
    <input type="text" name="name" id="name" class="form-control" placeholder="Pig name" aria-label="First name" autocomplete="given-name" hidden>

        </div>
</div>
<br>
<div class="row">
        
      
        <div class="col">
        <label for="weight">Weight Class</label>
  <select name="weightclass" id="weight" class="form-select form-select-sm" aria-label="weightclass" required>
  <option value="" disabled selected hidden>Select</option>
  <option value="30-40kg">30-40kg</option>
  <option value="40-50kg">40-50kg</option>
  <option value="50-60kg">50-60kg</option>
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



<!-- add piglets -->

<div class="table-data">
				<div class="order">
        <?php
$sqlGroups = "SELECT DISTINCT id, sowname FROM tblgrowingphase WHERE status = 'grower'";
$queryGroups = $dbh->prepare($sqlGroups);
$queryGroups->execute();
$availableGroups = $queryGroups->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="left">
    <h1>Piglets List</h1>
    <div class="filter-group">
        <label for="groupFilters">Filter by Group:</label>
        <select id="groupFilters">
            <option value="all">All Pigs</option>
            <?php
            foreach ($availableGroups as $group) {
                $value = $group['sowname'];
                echo "<option value='$value'>$group[sowname]</option>";
            }
            ?>
        </select>
    </div>

                    <button type="button" title="Click to Add" data-bs-toggle="modal" data-bs-target="#sellModal"
    class="openModalBtn" ><i class='bx bx-plus-circle'></i> Add New</button>
				</div>
                <table id="myTable">
						<thead>
							<tr>  
                                <th>ID</th>
                                <th>Image</th>
								                <th>Piglet Name</th>
                                <th>Sex</th>
							                	<th>Age</th>
                                <th>Weight</th>
                                <th>Price</th>
                                <th>Creation Date</th>  
                                <th>Action</th>
                                
							</tr>
						</thead>
                        
						<tbody>
                        <?php 
                          
                          $sqlpiglets = "SELECT pd.*,pd.piglet_id AS id, pd.name AS groupname, ps.farrowed_Date AS farrowed_date
                          FROM tblpiglet_for_sale_details pd
                          LEFT JOIN tblpiglet_for_sale ps ON pd.tblpiglet_for_sale_id = ps.id
                          WHERE pd.status = 'AVAILABLE'";
                          $querypiglets = $dbh->prepare($sqlpiglets);
                          $querypiglets->execute();
                          $resultspiglets=$querypiglets->fetchAll(PDO::FETCH_OBJ);
                          
                          foreach($resultspiglets as $resultpigletslist){
                            // age
$weaningDate = new DateTime($resultpigletslist->farrowed_date);
$currentDate = new DateTime();  
$weaningDate->setTime(0, 0, 0);
$currentDate->setTime(0, 0, 0);
$interval = $currentDate->diff($weaningDate);

$daysDifference = $interval->days;
$age = $daysDifference;
// age

                              $farrrowed = new DateTime($resultpigletslist->farrowed_date );
                              $farrrowedddate = $farrrowed->format('F j, Y');
                          
                          ?>
                              
                              <tr>
	<td>
	<p><?php echo htmlentities($resultpigletslist->id); ?></p>
		</td>
	<td>
	<img src="img/img_piglets_for_sale/<?php echo htmlentities($resultpigletslist->img); ?>" alt="_blank" title="<?php echo htmlentities($resultpigletslist->img); ?>">
		</td>
	<td><?php echo htmlentities($resultpigletslist->groupname); ?></td>
	<td><?php echo htmlentities($resultpigletslist->gender); ?></td>  
    <td><?php echo htmlentities($age); ?></td>
    <td><?php echo htmlentities($resultpigletslist->piglet_weight);?> kg</td>
    <td><span>&#8369;</span><?php echo htmlentities($resultpigletslist->price); ?></td>
    <td><?php echo htmlentities($farrrowedddate); ?></td>
    <!-- Button trigger modal -->
    <td class="action">
      <button type="button" class="btn delete" title="Delete Pig"  data-bs-toggle="modal" data-bs-target="#deletepigletModal-<?php echo htmlentities($resultpigletslist->id); ?>"><i class='bx bx-trash'></i></button>
    <button type="button" class="btn btn-sm updateModalBtnPiglets" title="Update Piglets" data-bs-toggle="modal" data-bs-target="#updateModalpiglets" data-forpigletsid="<?php echo $resultpigletslist->id; ?>"><i class='bx bx-edit'></i></button>
                          </td>
    <!-- Button trigger modal -->
  </tr>
  
<!-- deletepig  Modal -->
<div class="modal fade" id="deletepigletModal-<?php echo htmlentities($resultpigletslist->id); ?>" tabindex="-1"  aria-labelledby="cancelModalLabel-<?php echo htmlentities($resultpigletslist->id); ?>" aria-hidden="true">
       
<div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/deletepig.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3 class="confirm">Are you sure you want to delete this piglet?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="deletepiglet('<?php echo htmlentities($resultpigletslist->id); ?>')" name="delete">Confirm</button>
                </div>
            </div>
        </div>
    </div>

<!-- delete pig Modal -->

<!-- update pig Modal -->
<div class="modal fade" id="updateModalpiglets" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Piglet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateFormpiglets" action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
          
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="updatepiglets" class="btn btn-primary">Update</button>
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

        <div class ="row">
      <div class="col">
      <label for="pigletsgrouplist">Pig Group</label>
      <select
              id="pigletsgrouplist"
              name="sow"
              class="form-select form-select-sm"
              required="required"
              onchange="pigletsgroupchange()">
            
              <?php echo $pigletgroup; ?>
            </select>
        </div>
</div>

        <div class="row">
        <!-- <input type="hidden" name="id" class="form-control" placeholder="Pig name" aria-label="First name" value="" disabled>
        <input type="hidden" name="group_id" id="piglet_group_id" class="form-control"> -->

        <div class="col">
  <label for="childSelectpiglets">Piglets</label>
  <select name="pigletname"  class="form-select form-select-sm" id="childSelectpiglets"
   onchange="updatpigletsellSelect()">
  <option value="">Select Piglet</option>
</select>
  </div>
  <input type="hidden" name="piglets_id" id="piglets_id" class="form-control">

        <!-- <div class="col">
    <label for="groupname">Name</label>
        <input type="text" id="group_name" name="name" class="form-control" placeholder="Pig name" aria-label="First name" autocomplete="given name" readonly>
    </div> -->
    <div class="col">
            <label for="group_farrowed">Farrowed Date</label>
        <input type="date" name="farrowed" id="group_farrowed" class="form-control" placeholder="farrowed date" aria-label="farrowed date" readonly>
            </div>




    </div>
    <hr class="border border-dark border-1 opacity-100">
    <div class="row">
        <h5>Add Price per Piglet</h5>
    </div>
    <div class="row">
    <div class="col">
    <label for="pictpiglets">Picture</label>
    <input type="file" id="pictpiglets" name="pictpiglets[]" class="form-control" multiple>
    <input type="hidden" id="piglet-prices" name="piglet-prices" class="form-control form-control-sm rounded-0">
</div>
<div class="col">
<label for="pigletgender">Gender</label>
<select name="pigletgender" id="pigletgender" class="form-select form-select-sm" aria-label="weightclass" disabled>
  <option selected>Select</option>
  <option value="Male">Male</option>
  <option value="Female">Female</option>
</select>
</div>
<div class="col">
    <label for="pigletweight">Weight</label>
    <input type="number" id="pigletweight" name = "pigweight" class="form-control" placeholder="Kg">
</div>

<div class="col">
    <label for="priceInput">Price</label>
    <input type="number" id="priceInput" name = "priceInput" class="form-control" placeholder="Pesos">
</div>

<div class="col d-flex flex-column">
    <input type="button" class="form-control btn btn-dark mt-auto" id="price-add"  value="ADD">
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
                            <th scope="col" class="text-center">Image</th>
                            <th scope="col" class="text-center">Name</th>
                            <th scope="col" class="text-center">Gender </th>
                            <th scope="col" class="text-center">Weight</th>
                            <th scope="col" class="text-center">Price</th>
                            <th scope="col" class="text-center">Action</th>
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


  
        <div class="table-data d-none">
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



//pglets

$(document).on("click", ".updateModalBtnPiglets", function() {
  var forpigletsId = $(this).attr("data-forpigletsid"); 
  $.ajax({
    url: 'getpigdata.php', 
    type: 'POST',
    data: { forpigletsId: forpigletsId},  
    dataType: 'json',  
    success: function(response) {
      $("#updateFormpiglets").html(`
      <div class="row">
      <input type="hidden" name="id" value="${response.id}">
            <input type="hidden" name="main_id" value="${response.main_id}">
  <div class="col">
  <label for="fullname">Piglet Name</label>
    <input type="text" name="groupname" class="form-control" value="${response.name}">
  </div>
  <div class="col">
  <label for="fullname">Farrowed Date</label>
    <input type="text" name="farrow" class="form-control" value="${response.farrowed}">
  </div>
 
</div>


<br>
<div class="row">
    <div class="col">
  <label for="fullname">Gender</label>
 <select name="gender" class="form-select form-select-sm" aria-label="Large select example">
  <option value="Male" ${response.gender === "Male" ? "selected" : ""}>Male</option>
  <option value="Female" ${response.gender === "Female" ? "selected" : ""}>Female</option>
</select>
  </div>

          <div class="col">
  <label for="fullname">Weight</label>
  <input type="text" name="piglet_weight" class="form-control" value="${response.piglet_weight}">
  </div>
     <div class="col">
  <label for="fullname">Price</label>
  <input type="text" name="price" class="form-control" value="${response.price}">
</div>
      </div>
      <br>
      <div class="row">
   <div class="col">
  <label>Add New Image:</label>
  <input type="file" id="img" name="img" class="form-control form-control-sm rounded-0">
  <br>
  <img src="img/img_piglets_for_sale/${response.img}" class="rounded mx-auto d-block" alt="pig" width="150px" height="100px">
</div>
</div>
      `);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);  
    }
    
  });

});

//pglets



//sell pglets
function pigletsgroupchange() {
    var farrowed_date = document.getElementById("group_farrowed");
  var parentSelect = document.getElementById('pigletsgrouplist');
    var childSelect = document.getElementById('childSelectpiglets');
    var selectedParentId = parentSelect.value;

    childSelect.innerHTML = '<option value="">Piglets</option>';

    if (selectedParentId) {
      fetch('getChildOptions.php?pigletid=' + selectedParentId)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();  
    })
    .then(data => {
      console.log('Response:', data);
      if(data.weaned_date){
        farrowed_date.value = data.weaned_date;
      }
        if (Array.isArray(data.piglets)) {  
            data.piglets.forEach(function(child) {
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


function updatpigletsellSelect() {
    var farrowed_date =  document.getElementById('group_farrowed');
    let pigletIdField = document.getElementById('piglets_id');
    let genderField = document.getElementById('pigletgender');

    var pigletsgroupselect = document.getElementById('childSelectpiglets');
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
                    genderField.value = data.gender;  
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
          let piglet_id = document.getElementById("piglets_id").value;
            let img = document.getElementById("pictpiglets");
            let pigletSelect = document.getElementById("childSelectpiglets");
            let pigletId = pigletSelect.value;
            let name = pigletSelect.options[pigletSelect.selectedIndex].text;
            let weight = document.getElementById("pigletweight");
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
    });


//sell pglets







$(document).ready(function () {
  
    $('#myTable').DataTable();

    $('#mysecondTable').DataTable();

    var dataTable = $('#myTable').DataTable();

$('#groupFilter').on('change', function() {
    var selectedValue = $(this).val();

    if (selectedValue === 'all') {
        dataTable.search('').draw();
    } else {
        dataTable.column(6)  
            .search(selectedValue)
            .draw();
    }
});
    $(document).on('click', '.delete-btn', function() {
        deletePigId = $(this).data('id');
        $('#deleteModal-' + deletePigId).modal('show');
    });

    $(document).on('click', '#confirmDelete', function() {
        deletepig(deletePigId);
    });


    $(document).on('click', '.delete', function() {
        deletefeedId = $(this).data('id');
        $('#deletefeedModal-' + deletefeedId).modal('show');
    });

    $(document).on('click', '#confirmDelete', function() {
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



function updatpigletSelect() {
    let name = document.getElementById('name');
    let age = document.getElementById('month');
    var pigletsgroupselect = document.getElementById('childSelect');
    var selectedpigletId = pigletsgroupselect.value;

    if (selectedpigletId) {
        fetch('getChildOptions.php?piglet_name=' + selectedpigletId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data && !data.error) {
                  name.value = data.name;
                  age.value = data.age;
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
    $.ajax({
        url: 'delete.php',  
        type: 'POST',
        data: { feedid: id },
        success: function(response) {
            $('#deletefeedModal-' + id).modal('hide');
            location.reload();
        },
        error: function() {
            alert('An error occurred while trying to delete the feed.');
        }
    });
}

$(document).on('click', '.deletepiglet-btn', function() {
        deletePigId = $(this).data('id');
        $('#deletepigletModal-' + deletePigId).modal('show');
    });

    $(document).on('click', '#confirmDelete', function() {
      deletepiglet(deletePigId);
    });

function deletepiglet(id) {
alert(id);

    $.ajax({
        url: 'delete.php',
        type: 'POST',
        data: { pigletid: id },
        success: function(response) {
            
            try {
                const json = JSON.parse(response);
                if (json.success) {
                
                    $('#deletepigletModal-' + id).modal('hide');
                  
                    location.reload();
                } else {
                    alert("Delete failed: " + json.error);
                }
            } catch (e) {
                console.error("Invalid JSON:", response);
                alert("Unexpected response from server.");
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
            alert('An error occurred while trying to delete the piglet.');
        }
    });
}



function deletepig(id) {
    $.ajax({
        url: 'delete.php',  
        type: 'POST',
        data: { id: id },
        success: function(response) {
            $('#deleteModal-' + id).modal('hide');
            location.reload();
        },
        error: function() {
            alert('An error occurred while trying to delete the pig.');
        }
    });
}




    $(document).on("click", ".updateModalBtn", function() {
  var pigId = $(this).attr("data-pigid"); 
  $.ajax({
    url: 'getpigdata.php', 
    type: 'POST',
    data: { pigId: pigId},  
    dataType: 'json',  
    success: function(response) {
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
      console.log(textStatus, errorThrown);  
    }
    
  });

});


$(document).on("click", ".updatefeed", function() {
  var feedId = $(this).attr("data-feedid");  
  
  $.ajax({
    url: 'getfeed.php',  
    type: 'POST',
    data: { feedId: feedId },   
    dataType: 'json',   
    success: function(response) {
      console.log(response);
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
      console.log(textStatus, errorThrown);  
    }
  });

});

</script>
	<script src="script.js"></script>
</body>
</html>
<?php } ?>