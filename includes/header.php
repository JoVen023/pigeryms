<?php

if (isset($_SESSION['customer'])) {
  // Access the value associated with the 'login' key
  $email = $_SESSION['customer'];
  $status = "Pending";
  $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblorders WHERE cust_id = :email AND orderstatus = :status"); 
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->execute(); 
  $orderCount = $stmt->fetchColumn();

  // Now check if there are any orders.
  if ($orderCount > 0) {
      $class = "red-dot";
  } else {
      $class = "";
  }
 
} else {
  // The 'login' key doesn't exist, handle the error
  $email = false; // or any default value you want
}
$num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; 



?>
<header>
  <div class="default-header">
    <div class="container">
      <div class="row">
        <div class="col-sm-2 col-md-2">
          <div class="logo"><a href="index.php"><img src="img/logos.jpeg" alt="image" width="100" height="100"><span style="color: #00bf63;"></div>
          

        </div>
        <div class="col-sm-9 col-md-10">
          <div class="header_info">
          
            <div class="header_widgets">
              <div class="circle_icon"> <i class="fa fa-envelope" aria-hidden="true"></i> </div>
              <p class="uppercase_text">For Support Email us : </p>
              <a href="mailto:info@example.com">Lasdocejerome@gmail.com</a> </div>
            <div class="header_widgets">
              <div class="circle_icon"> <i class="fa fa-phone" aria-hidden="true"></i> </div>
              <p class="uppercase_text">Service Hotline Call Us: </p>
              <a href="tel:61-1234-5678-09">09262026959</a> </div>
            <div class="social-follow">
              <ul>
                <li><a href="https://code-projects.org/"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
                <li><a href="https://code-projects.org/"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></li>
                <li><a href="https://code-projects.org/"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a></li>
                <li><a href="https://code-projects.org/"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a></li>
                <li><a href="https://code-projects.org/"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
              </ul>
            </div>

 <div class="login_btn"><a href="cart.php" ><i class="fa fa-shopping-cart" aria-hidden="true"> <span class="cart-count"><?php echo $num_items_in_cart ?></span></i></a></div>


          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <nav id="navigation_bar" class="navbar navbar-default fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button id="menu_slide" data-target="#navigation" aria-expanded="false" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
         <span class="icon-bar"></span> <span class="icon-bar"></span></button>
      </div>
      <div class="header_wrap">
        <div class="user_login">
          <ul>
            <li class="dropdown"> <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-circle" aria-hidden="true">
            </i>
<?php

$sql ="SELECT FullName FROM tblusers WHERE EmailId=:email ";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
	{
	 echo htmlentities($result->FullName); }}?><i class="fa fa-angle-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu dropdown-left">
           <?php if($email){?>
          <li><a href="profile.php">Profile Settings</a></li>
              <li><a href="update-password.php">Update Password</a></li>
            <li><a href="#" id="logoutLink">Sign Out</a></li>
            <?php } else { ?>
          
            <?php } ?>
          </ul>
            </li>
          </ul>
        </div>
       
      </div>
      
      <div class="collapse navbar-collapse" id="navigation">
      <div class="d-flex justify-content-center">
        <ul class="nav navbar-nav" id="line">
          <li class="custom-link" ><a href="index.php" class="nav-link" href="#scrollspyHeading1">Home</a></li>
          <li class="custom-link" ><a href="pig-list.php"class="nav-link" href="#scrollspyHeading2">Shop</a>
          <li class="custom-link dot" ><a href="pigletsforsale.php" class="nav-link"href="#scrollspyHeading3">Piglets<span class=" <?php echo $class; ?>"></span></a></li>
         <li class="custom-link dot" ><a href="cull.php" class="nav-link"href="#scrollspyHeading3">Cull<span class=" <?php echo $class; ?>"></span></a></li>
         <li class="custom-link dot" ><a href="my-order.php" class="nav-link"href="#scrollspyHeading3">My Order<span class=" <?php echo $class; ?>"></span></a></li>
        </ul>
      </div>
      </div>
    </div>
  </nav>
  <!-- Navigation end --> 

</header>


<script>
document.getElementById("logoutLink").addEventListener("click", function(e) {
    e.preventDefault(); // prevent the default link click action
    var confirmAction = confirm("Are you sure you want to log out?");
    if (confirmAction) {
        // If user confirms logout, redirect to logout.php
        window.location.href = "logout.php";
    }
});
</script>