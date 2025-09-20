<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
  $_SESSION['sidebarname'] ='Manage Pages';

    $query = $dbh->prepare("SELECT * FROM tblmanage");
    try {
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
      echo $ex->getTraceAsString();
      echo $ex->getMessage();
      exit;
    }

    $id = $result['id'];

    if(isset($_POST['home'])) {
        $tag = $_POST['tag'];
        if ($_FILES['homepict']['error'] == UPLOAD_ERR_OK) { 
            $filename =basename($_FILES['homepict']['name']);
    
            $uploadPath = 'img/' . $filename;
    
            if (move_uploaded_file($_FILES['homepict']['tmp_name'], $uploadPath)) {
                $query = $dbh->prepare("UPDATE tblmanage SET tag=:tag, img=:img WHERE id=:id");
    
                $query->bindParam(':tag', $tag, PDO::PARAM_STR);
                $query->bindParam(':img', $filename, PDO::PARAM_STR);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
                try {
                    $query->execute();
                    if($query){
                      $success = "Updated Successfully" && header("refresh:1;url=managepage.php");
            
                    }else{
                      $error = "Please try again Later";
                    }
                    // echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'managepage.php';</script>";
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

    if(isset($_POST['contact'])) {
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $telephone = $_POST['telephone'];
        
      
                $query = $dbh->prepare("UPDATE tblmanage SET emailaddress=:email, mobilenumber=:mobile,phonenumber=:telephone WHERE id=:id");
    
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':mobile', $mobile, PDO::PARAM_INT);
                $query->bindParam(':telephone', $telephone, PDO::PARAM_INT);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
    
                try {
                    $query->execute();
                    if($query){
                      $success = "Updated Successfully" && header("url=managepage.php");
            
                    }else{
                      $error = "Please try again Later";
                    }
                    // echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'managepage.php';</script>";
                } catch (PDOException $ex) {
                    echo $ex->getMessage();
                    exit;
                }
        }
  
        if(isset($_POST['about'])) {
            $abouts = $_POST['aboutus'];
            $products = $_POST['products'];
            
            if ($_FILES['map']['error'] == UPLOAD_ERR_OK) {  
               
                $filename =basename($_FILES['map']['name']);
        
            
                $uploadPath = 'img/' . $filename;
        
            
                if (move_uploaded_file($_FILES['map']['tmp_name'], $uploadPath)) {
                     
                    $query = $dbh->prepare("UPDATE tblmanage SET about=:abouts,products=:products, map=:img WHERE id=:id");
        
                   
                    $query->bindParam(':abouts', $abouts, PDO::PARAM_STR);
                    $query->bindParam(':products', $products, PDO::PARAM_STR);
                    $query->bindParam(':img', $filename, PDO::PARAM_STR);
                    $query->bindParam(':id', $id, PDO::PARAM_INT);
        
                
                    try {
                        $query->execute();
                        if($query){
                          $success = "Updated Successfully" && header("url=managepage.php");
                
                        }else{
                          $error = "Please try again Later";
                        }
                        // echo "<script type='text/javascript'>alert('Updated Successfully'); window.location.href = 'managepage.php';</script>";
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
	<title>Manage Page</title>
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
       
    <div class="card">
					<div class="card-header">
						<h3 class="card-title">Homepage</h3>

					</div>
					<div class="card-body">
						<form method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
							<input type="hidden" name="hidden_id" value="<?php  echo $result['id'];?>" />
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <p  class="managepict" >Picture</p>
                                <img src="img/<?php echo htmlspecialchars($result['img']); ?>" class="rounded mx-auto d-block" alt="map" width="200px" height="200px">
								</div>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
									<label for="tags">Tag Line</label>
									<input id="tags" name="tag" class="form-control form-control-sm rounded-0"  value="<?php echo $result['tag'];?>" readonly/>
								</div>
								<div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
									<span>&nbsp;</span>
                  <br>
									<button type="button" class="btn btn-primary btn-sm btn-flat btn-block" data-bs-toggle="modal" data-bs-target="#exampleM">
  Update
</button>
								</div>

                                <div class="modal fade" id="exampleM" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Update Homepage</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="col">
      <br>
      <div class="row">
                                <label for="homepict">Add new Image:</label>
  									<input type="file" id="homepict" name="homepict"  class="form-control form-control-sm rounded-0">
      </div>
                 <br>
                  <div class="row">
                    <img src="img/<?php echo htmlspecialchars($result['img']); ?>" class="rounded mx-auto d-block" alt="map" width="300px" height="250px">
								</div>
                <br>
								<div class="row">
									<label for="tag">Tag Line</label>
									<input id="tag" name="tag" class="form-control form-control-sm rounded-0"  value="<?php echo $result['tag'];?>" />
								</div>
								
									

    </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="home" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
							</div>
						
					</div>
					<!-- /.card-body -->

				</div>
               
                <div class="card">
					<div class="card-header">
						<h3 class="card-title">Contact Details</h3>

					</div>
					<div class="card-body">
							<div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
									<label for="email">Email Address</label>
									<input id="email" name="email" class="form-control form-control-sm rounded-0" autocomplete="off" value="<?php echo $result['emailaddress'];?>" />
								</div>
           
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
									<label for="mobile">Mobile Number</label>
									<input id="mobiles" name="mobile" class="form-control form-control-sm rounded-0"  value="<?php echo $result['mobilenumber'];?>" />
								</div>
                <br>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
									<label for="telephones">Telephone Number</label>
									<input id="telephones" name="telephone" class="form-control form-control-sm rounded-0"  value="<?php echo $result['phonenumber'];?>" />
								</div>
								<div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
									<span>&nbsp;</span>
                    
                                    <button type="button" class="btn btn-primary btn-sm btn-flat btn-block" data-bs-toggle="modal" data-bs-target="#example">
  Update
</button>
								</div>
							

                                <div class="modal fade" id="example" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Update Contact Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="col">
      <div class="row">
      <br>
									<label for="emails">Email Address</label>
									<input id="emails" name="email" class="form-control form-control-sm rounded-0" autocomplete="off"  value="<?php echo $result['emailaddress'];?>" />
								</div>
                <br>
								<div class="row">
									<label for="mobile">Mobile Number</label>
									<input id="mobile" name="mobile" class="form-control form-control-sm rounded-0"  value="<?php echo $result['mobilenumber'];?>" />
								</div>
                <br>
								<div class="row">
									<label for="telephone">Telephone Number</label>
									<input id="telephone" name="telephone" class="form-control form-control-sm rounded-0"  value="<?php echo $result['phonenumber'];?>" />
								</div>

    </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="contact" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</div>
							</div>

      </div>


					<!-- /.card-body -->

				
                <div class="card">
					<div class="card-header">
						<h3 class="card-title">About Us</h3>
					</div>
					<div class="card-body">
						
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                 <p class="managepict">Picture</p>
                                 <img src="img/<?php echo htmlspecialchars($result['map']); ?>" class="rounded mx-auto d-block" alt="map" width="200px" height="200px">
  									
								</div>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
									<label for="textDatas">About</label>
                                    <textarea id="textDatas" name="aboutus" class="form-control form-control-sm rounded-0"  rows="6" cols="50" readonly><?php echo $result['about'];?></textarea><br>
                                   
								</div>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
									<label for="text">Products</label>
                                    <textarea id="text" class="form-control form-control-sm rounded-0"  rows="6" cols="50" readonly><?php echo $result['products'];?></textarea><br>
									
								</div>
								<div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
									<span>&nbsp;</span>
                                    <button type="button" class="btn btn-primary btn-sm btn-flat btn-block" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Update
</button>
								
								</div>

                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Update About Page</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="col">
								<div class="row">
                                 <label for="map">Add new Image:</label>
  									<input type="file" id="map" name="map" class="form-control form-control-sm rounded-0">
                    <div>
                      <br>
                    <div class="row">
                    <img src="img/<?php echo htmlspecialchars($result['map']); ?>" class="rounded mx-auto d-block" alt="map" width="200px" height="200px">
                  </div>
                  <br>
								<div class="row">
									<label for="textDat">About</label>
                                    <textarea id="textDat" name="aboutus" class="form-control form-control-sm rounded-0" name="products" rows="4" cols="50"><?php echo $result['about'];?></textarea><br>
								</div>
                                 <br>
								<div class="row">
									<label for="textData">Products</label>
                                    <textarea id="textData" class="form-control form-control-sm rounded-0" name="products" rows="4" cols="50"><?php echo $result['products'];?></textarea><br>
								</div>
									

    </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="about" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
							</div>
						</form>
					</div>
					<!-- /.card-body -->

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
$(document).ready(function () {
    // Initialize DataTable
    $('#myTable').DataTable();
});
</script>

	<script src="script.js"></script>
	
</body>
</html>
<?php } ?>
