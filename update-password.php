
<?php
include('./includes/config.php');
if(strlen($_SESSION['login']) == 0) { 
    header('location:index.php');
    exit(); // Always exit after a header redirect
} else {


  $error='';
  $msg='';
    if(isset($_POST['update'])) {
        $password = $_POST['password'];
        $newpassword = $_POST['newpassword'];
        $email = $_SESSION['login'];

        // Get the stored password associated with the given email
        $sql = "SELECT Password FROM tblusers WHERE EmailId=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        // Fetch the stored password from the result
        $results = $query->fetch(PDO::FETCH_ASSOC);
        if($results) {
            $storedPassword = $results['Password'];

            // Verify the input password against the stored password
            if(password_verify($password, $storedPassword)) {
                
                // Hash the new password
                $hashedNewPassword = password_hash($newpassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $con = "UPDATE tblusers SET Password=:newpassword WHERE EmailId=:email";
                $chngpwd1 = $dbh->prepare($con);
                $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
                $chngpwd1->bindParam(':newpassword', $hashedNewPassword, PDO::PARAM_STR);
                $chngpwd1->execute();

                $msg = "Your Password succesfully changed";
            } else {
                $error = "Your current password is wrong";
            }
        } else {
            $error = "An error occurred while fetching the password from the database";
        }
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
<title>Ronald's Baboyan | Update Password</title>
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
    .errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
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
        <h1>Update Password</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="index.php">Home</a></li>
        <li>Update Password</li>
      </ul>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>
<!-- /Page Header--> 

<section class="user_profile inner_pages">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-sm-3">
        <?php include('includes/sidebar.php');?>
      <div class="col-md-6 col-sm-8">
        <div class="profile_wrap" style="">
        <form method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return valid();">
             <?php if($error){?><div class="errorWrap" style="color:red;"><strong>ERROR</strong>:<?php echo $error; ?></div><?php } 
        else if($msg){?><div class="succWrap" style="color:green;"><strong>SUCCESS</strong>:<?php echo $msg; ?> </div><?php }?>
            <div class="contact_form ">
              <label class="control-label">Password</label>
              <input class="form-control black-bg" id="password" name="password"  type="password" required>
            </div>
           
            <div class="contact_form">
              <label class="control-label">New Password</label>
              <input class="form-control black_bg" id="newpassword" type="password" name="newpassword" required>
            </div>
            <div class="contact_form ">
              <label class="control-label">Confirm Password</label>
              <input class="form-control black_bg" id="confirmpassword" type="password" name="confirmpassword"  required>
            </div>
          <br>
            <div class="contact_form">
               <input type="submit" value="Update" name="update" id="submit" class="btn btn-block" style="background-color:rgb(20, 150, 15);">
            </div>
          </form>
          </div>
      </div>
    </div>
  </div>
</div>
       
    
</section>
<!--/Profile-setting--> 

<!--Footer -->
<?php include('includes/footerhome.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 
<script>
function valid() {
  if (document.getElementById('newpassword').value !== document.getElementById('confirmpassword').value) {
    alert("New Password and Confirm Password do not match!");
    document.getElementById('confirmpassword').value = ''; // Clear the Confirm Password field
    document.getElementById('confirmpassword').focus();
    return false;
  }
  return true;
}
</script>
<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->

<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 

<script src="assets/js/owl.carousel.min.js"></script>


</body>
</html>
<?php } ?>