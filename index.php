<?php 

include './includes/config.php';

if (!isset($_SESSION['customer'])) {
  header("Location: login.php");
  exit();
}
$query = $dbh->prepare("SELECT img,tag FROM tblmanage");
try {
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
  echo $ex->getTraceAsString();
  echo $ex->getMessage();
  exit;
}

?>
<!DOCTYPE HTML>
<html lang="en">
<head>

<title>Ronald's Baboyan</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<link href="assets/css/slick.css" rel="stylesheet">
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<link rel="shortcut icon" href="assets/images/logos.jpeg">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 

</head>
<body>
   
<!--Header-->
<?php include './includes/header.php';?>
<!-- /Header --> 

<!-- Banners -->
<section class="banner-section">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-push-7">
        <div class="banner_content">
          <h1><span class="derit-color">Ronald's</span> Baboyan</h1>
          <p><?php echo htmlspecialchars($result['tag']);?></p>
          <a href="pig-list.php" class="btn">Shop Now <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
        </div>
      </div>
    </div>
  </div>
  <div class="background_image" style="background-image: url('admin/img/<?php echo htmlspecialchars($result['img']); ?>');"></div>
</section>
<!-- /Banners --> 
<!--Footer -->
<?php
include 'includes/footerhome.php';
   ?>
<!-- /Footer--> 

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