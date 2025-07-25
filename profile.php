<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['customer'])==0)
  { 
header('location:index.php');
}
else{
if(isset($_POST['updateprofile']))
  {
$name=$_POST['fullname'];
$mobileno=$_POST['phoneNumber'];
$dob=$_POST['dob'];
$adress=$_POST['address'];
$id=$_SESSION['customer'];

$sql="update tblusers set FullName=:name,ContactNo=:mobileno,dob=:dob,Address=:adress where id=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':name',$name,PDO::PARAM_STR);
$query->bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query->bindParam(':dob',$dob,PDO::PARAM_STR);
$query->bindParam(':adress',$adress,PDO::PARAM_STR);

$query->bindParam(':id',$id,PDO::PARAM_STR);
$query->execute();
$msg="Profile Updated Successfully";
header('location: profile.php');
exit;
}

?>
  <!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<title>Ronald's Baboyan | My Profile</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="shortcut icon" href="assets/images/logos.jpeg">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
 <style>




    </style>
</head>
<body>
        
<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 
<!--Page Header-->
<section class="page-header profile_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>My Profile</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li>Profile</li>
      </ul>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>
<!-- /Page Header--> 


<?php 
$useremail=$_SESSION['customer'];
$sql = "SELECT * from tblusers where id=:useremail";
$query = $dbh -> prepare($sql);
$query -> bindParam(':useremail',$useremail, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ 
  $dobDate = new DateTime($result->dob);
  $formattedDob = $dobDate->format('F j, Y');

  // Format "regDate" using the DateTime class
  $regDate = new DateTime($result->RegDate);
  $formattedRegDate = $regDate->format('F j, Y');
  ?>
<section class="user_profile inner_pages" >
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-sm-3">
        <?php include('includes/sidebar.php');?>
      <div class="col-md-6 col-sm-8">
        <div class="profile_wrap">
          <h5 class="uppercase">General Settings</h5>
          <?php  
         if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
          

           <div class="form-groups" id="forms">
              <label class="control-label" style="color:#000">Reg Date:</label>
             <p style="color:#000"><?php echo htmlentities($formattedRegDate);?></p>
            </div>
             <?php if($result->UpdationDate!=""){
              $dateFromDb = $result->UpdationDate; // This is the date from the database
              $timestamp = strtotime($dateFromDb); // Convert the date into a Unix timestamp
              
              $formattedDate = date("F j, Y g:i a", $timestamp); // Format the date
              
              
              ?>
            <div class="form-groups">
              <label class="control-label">Last Updated:</label>
              <p style="color:#000"><?php echo htmlentities($formattedDate);?></p>
            </div>
            <?php } ?>
            <div class="form-groups">
              <label class="control-label">Full Name</label>
              <input class="form-control white_bg"  value="<?php echo htmlentities($result->FullName);?>"  readonly>
            </div>
            <div class="form-groups">
              <label class="control-label">Email Address</label>
              <input class="form-control white_bg" value="<?php echo htmlentities($result->EmailId);?>"  readonly>
            </div>
            <div class="form-groups">
              <label class="control-label">Phone Number</label>
              <input class="form-control white_bg" name="mobilenumber" value="<?php echo htmlentities($result->ContactNo);?>" id="phone-number" type="text" readonly>
            </div>
            <div class="form-groups">
              <label class="control-label">Date of Birth</label>
              <input class="form-control white_bg" value="<?php echo htmlentities($formattedDob);?>"  placeholder="dd/mm/yyyy" id="birth-date" type="text" readonly>
            </div>
            <div class="form-groups">
              <label class="control-label">Address</label>
              <input class="form-control white_bg" value=" <?php echo htmlentities($result->Address);?>"  placeholder="dd/mm/yyyy" readonly>
               
            </div>
          
        
            <div class="form-group">
    <button id="openFormButton" class="btn btn-primary" data-toggle="modal" data-target="#updateModal">Update Information</button>
</div>
</div>
      </div>
    </div>
  </div>
             </div>
             

<!-- The Modal -->
<div id="updateModal" class="modal fade" >
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="text-center">
                    <img src="img/profile.svg" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
                    <h3 style="margin-top: 10px; font-weight: bold;color:#000">Update Information</h3>
                  </div>
            </div>
          
<!-- Modal body -->
<div class="modal-body">
    <form id="updateForm" action="<?=$_SERVER['PHP_SELF']?>">
        <div class="form-group">
            <label for="fullname">Full Name:</label>
            <input class="form-control white_bg" name="fullname"  id="fullname" type="text" value="<?php echo htmlentities($result->FullName);?>" required>
        </div>
        <div class="form-group">
            <label for="phoneNumber">Phone Number</label>
            <input class="form-control white_bg" name="phoneNumber" id="phoneNumber" type="text" value="<?php echo htmlentities($result->ContactNo);?>" required>
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input class="form-control white_bg" name="dob" id="dob" type="text" value="<?php echo htmlentities($result->dob);?>">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input class="form-control white_bg" name="address" id="address" type="text" value="<?php echo htmlentities($result->Address);?>">
        </div>
        
   
</div>
<?php }} ?>
           
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color:#000">Cancel</button>
                <button type="submit" name="updateprofile" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
    </form>
</div>
</section>
<br>
<br>
<br>
<!--/Profile-setting--> 

<!--Footer -->
<?php include('includes/footerhome.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 

<!--customer-Form -->
<?php include('includes/customer.php');?>
<!--/customer-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>
<!--/Forgot-password-Form --> 
<script>
    // Attach the submit event listener to the updateForm
    updateForm.addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Retrieve the form field values
        const fullName = document.querySelector('#updateModal [name="fullname"]').value;
        const phoneNumber = document.querySelector('#updateModal [name="phoneNumber"]').value;
        const dob = document.querySelector('#updateModal [name="dob"]').value;
        const address = document.querySelector('#updateModal [name="address"]').value;

        // Create a new FormData object and append the form data
        const formData = new FormData();
        formData.append('fullname', fullName);
        formData.append('phoneNumber', phoneNumber);
        formData.append('dob', dob);
        formData.append('address', address);
        formData.append('updateprofile', true); // Add this to indicate the form submission

        // Create an XMLHttpRequest object
        const xhr = new XMLHttpRequest();

        // Define the AJAX request
        xhr.open('POST', '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Request successful
                    console.log(xhr.responseText);
                    // Reload the page to update the profile information
                    location.reload();
                } else {
                    // Request failed
                    console.error('Error:', xhr.status);
                }
            }
        };

        // Send the AJAX request with the form data
        xhr.send(formData);
    });
</script>
<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->

<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 

<script src="assets/js/owl.carousel.min.js"></script>


</body>
</html>
<?php } ?>