<?php
include 'includes/config.php';

$query = $dbh->prepare("SELECT about,products,map,mobilenumber,phonenumber,emailaddress FROM tblmanage");
try {
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
  echo $ex->getTraceAsString();
  echo $ex->getMessage();
  exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input data and sanitize them
    $userName = strip_tags(trim($_POST["name"]));
    $userEmail = filter_var(strip_tags(trim($_POST["email"])), FILTER_SANITIZE_EMAIL);
    $userMessage = strip_tags(trim($_POST["message"]));

    try {
        // Check if data is valid
        if (!empty($userName) && !empty($userEmail) && !empty($userMessage)) {
            // SQL query
            $sql = "INSERT INTO tblmessage (fullname, emailaddress, message) VALUES (:user_name, :user_email, :user_message)";

            // Prepare statement
            $stmt = $dbh->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':user_name', $userName);
            $stmt->bindParam(':user_email', $userEmail);
            $stmt->bindParam(':user_message', $userMessage);

            // Execute statement
            $stmt->execute();

            $success = "Message Sent Succesfully";
        } else {
            echo "Invalid input.";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en" id="top">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Ronald's Baboyan</title>

   <!-- Stylesheets -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
   <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
   <link rel="stylesheet" type="text/css" href="styles.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link href="assets/css/font-awesome.min.css" rel="stylesheet">
   <link rel="shortcut icon" href="assets/images/logos.jpeg">

   <!-- Scripts -->
   <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<script src="admin/js/swal.js"></script>
  </head>

  <?php if (isset($success)) { ?>
    <!--This code for injecting success alert-->
    <script>
        setTimeout(function() {
                swal("Success", "<?php echo $success; ?>", "success")
                .then((value) => {
                  window.location.href = 'landingpage.php';
                });
            },
            100);
    </script>
<?php } ?>
   <body>
   <nav class="navbar navbar-expand-lg fixed-top bg-white navbar-white">
  <div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
      <div class="d-flex align-items-center navlogo">
        <a class="navbar-brand" href="#home">
          <h5>
            <img src="img/logos.jpeg" alt="logo">
            <span style="color: #00bf63;">Ronald's</span> Baboyan
          </h5>
        </a>
      </div>
    </div>
    <div class="container-fluid navitems">
      <ul class="nav justify-content-center" style="font-size: 21px;">
        <li class="nav-item custom-link">
          <a class="nav-link" aria-current="page" href="#top">Home</a>
        </li>
        <li class="nav-item custom-link"> 
          <a class="nav-link" href="#contact">Contact Us</a>
        </li>
        <li class="nav-item custom-link">
          <a class="nav-link" href="#about">About Us</a>
        </li>
        <a href="login.php" class="btn btn-success fw-bold">Login</a>

      </ul>
    </div>
  </div>
</nav>


<!---------------------------------------------------------------------------------------------------->
<main>
  <!------------------------------Home---------------------------------------------------------------------->
<section id="home" class="section" style="padding-top:14vh">
<div class="container">
  <div class="text-column">
    <h1 style="font-size:7vh;">Welcome to <br> <span style="color: #00bf63;">Ronald's </span> Baboyan</h1>
    <br>
    <p class="none">"Discover healthy, well-cared-for pigs at Ronald's Baboyan. Buy directly from a trusted source for the finest quality."</p>
    <div class="text-center">
      <a href="#shop" class="btn-shops">Available Pigs</a>
      <a href="login.php?mode=signup" class="btn-shop">Sign Up Now</a>
    </div>
        </span>
  </div>

  <div class="image-column">
    <img src="img/barn.jpg" alt="Your Image">
  </div>
</div>
  </section>

<!-----------------------------------Pigs Available----------------------------------------------------------------->
<section id="shop" class="section">
<div class="contain">
    <h2 class="pigtitle">Available Pigs</h2>
    <p style="text-align: center;">We have pigs available in the following weight ranges.</p>
   
  <div class="card__container swiper" style="">
            <div class="card__content">
               <div class="swiper-wrapper">
               <?php
             $querys = "SELECT * FROM tblpigforsale WHERE status IS NULL OR status = ''";
             $stmts = $dbh->query($querys);
             $pigs = $stmts->fetchAll(PDO::FETCH_ASSOC);


?>
 <?php foreach ($pigs as $pig){
  $pricePerKg = (float) $pig['price'];
  $weightClass = $pig['weight_class'];
  
  preg_match_all('/\d+/', $weightClass, $matches);
  $minWeight = isset($matches[0][0]) ? (int) $matches[0][0] : 0;
  $maxWeight = isset($matches[0][1]) ? (int) $matches[0][1] : 0;

  $minPrice = $minWeight * $pricePerKg;
$maxPrice = $maxWeight * $pricePerKg;
  
  ?>
                  <article class="card__article swiper-slide">
                     <div class="card__image">
                        <img src="admin/img/<?php echo $pig['img']; ?>" alt="image" class="card__img" style="height:200px !important;">
                        <div class="card__shadow"></div>
                     </div>
      
                     <div class="card__data">
                        <h3 class="card__name"><?php echo $pig['name']; ?></h3>
                        <p class="card__description">
                        &#8369;<?php echo $pig['price']; ?>/kg
                        </p>
                        <p class="card__description">
                          <span>Projected Weight</span><br>
                        <?php echo $pig['weight_class']; ?><br>
                        <span>Projected Price</span><br>
                        &#8369;<?php echo number_format($minPrice); ?> -  
                        &#8369;<?php echo number_format($maxPrice); ?>
                        </p>
      
                        <a href="login.php" class="card__button">View More</a>
                     </div>
                  </article>
                  <?php } ?>
               </div>
            </div>
            <!-- Navigation buttons -->
            <div class="swiper-button-next">
               <i class="ri-arrow-right-s-line"></i>
            </div>
            <div class="swiper-button-prev">
               <i class="ri-arrow-left-s-line"></i>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
         </div>
 </div>
</section>

  
<!----------------------------------Contact Detials------------------------------------------------------------------>
  <section id="contact" class="section">
    <div class="contains" style="height:100%;">
  <div class="image-column">
  <img src="img/contact_us.svg" class="contact" alt="contact-us" />
  </div>
  <div class="text-column text">
  <div>
    <h3 class="contactdetails">Contact Details</h3>
</div>
<ul class="contacts">
  <li><h3><i class="fas fa-mobile-alt"></i> Mobile Number</h3> <?php echo htmlspecialchars($result['mobilenumber']);?></li>
  <li class="email"><h3><i class="fas fa-envelope"></i> Email Address</h3> <a href="mailto:info@example.com"><?php echo htmlspecialchars($result['emailaddress']);?></a></li>
  <li><h3><i class="fas fa-phone"></i> Telephone Number</h3> <?php echo htmlspecialchars($result['phonenumber']);?></li>
</ul>
</div>
</div>
  </section>


  <!----------------------------------Message------------------------------------------------------------------>
  <section id="message" class="section">
    <div class="contains">
  <div class="text-column">
  <div class="head">
    <h3 class="details">Message Us</h3>
</div>
<form action="<?=$_SERVER['PHP_SELF']?>" class="row g-3" method="post">
<br>
<div class="col-md-12">
    <label for="name" class="form-label">Full Name</label>
    <input type="text" name="name" class="form-control" id="name" required/>
    <br>
  </div>
  <div class="col-md-12">
    <label for="inputEmail4" class="form-label">Email Address</label>
    <input type="email"  name="email" class="form-control" id="inputEmail4" required/>
    <br>
  </div>
  
  <div class="col-md-12">
    <label class="form-label">Message</label>
    <div class="form-floating">
  <textarea class="form-control" id="floatingTextarea" name="message" required></textarea>
  <label for="floatingTextarea">Comments</label>
</div>
  </div>
  <div class="form-group">
        <input type="submit" value="Submit">
      </div>
</form>
</div>
<div class="image-column" style="">
  <img src="img/message.svg" class="contact" alt="contact-us" />
  </div>
</div>
 
  </section>
  


<!------------------------------------------About Us---------------------------------------------------------->
<br>
  <section id="about" class="section" style="padding-top: 10vh">
  <div class="aboutus">
  <div class="image-column">
    <img src="admin/img/<?php echo htmlspecialchars($result['map']);?>" alt="About us Image">
  </div>
  <div class="text-columns">
    <h1><span style="color: #00bf63;">Ronald's </span> Baboyan</h1>
    <p><?php echo htmlspecialchars($result['about']);?></p>
    <h1> Our <span style="color: #00bf63;">Products</span></h1>
    <p><?php echo htmlspecialchars($result['products']);?></p>
  
  </div>
 
</div>
  </section>
</main>
<br><br>

   <!-----footer---->
      <!--Back to top-->
      <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i></a> </div>
    <!--/Back to top-->
<?php
include 'includes/footer.php';
   ?>
<!----- end footer---->
         
<!-- Control Sidebar -->




<script>
        // Add an event listener to the links
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('.custom-link');
            links.forEach(function(link) {
                link.addEventListener('click', function() {
                    // Remove active class from all links
                    links.forEach(function(link) {  
                        link.classList.remove('active');
                    });
                    // Add active class to the clicked link
                    this.classList.add('active');
                });
            });
            const sections = document.querySelectorAll('.section');
      const windowHeight = window.innerHeight;

      function checkVisibility() {
        sections.forEach(function(section) {
          const position = section.getBoundingClientRect().top;

          if (position < windowHeight * 0.8 && position > -section.offsetHeight + windowHeight * 0.2) {
            section.classList.add('visible');
            section.classList.remove('invisible');
          } else {
            section.classList.remove('visible');
            section.classList.add('invisible');
          }
        });
      }

      window.addEventListener('scroll', checkVisibility);
      window.addEventListener('resize', checkVisibility);
      checkVisibility();
    });
    </script>
 <script>
   document.addEventListener('DOMContentLoaded', function() {
      const backToTopButton = document.getElementById('back-top');

      function toggleBackToTopButton() {
         if (window.scrollY > 0) {
            backToTopButton.style.display = 'block';
         } else {
            backToTopButton.style.display = 'none';
         }
      }

      window.addEventListener('scroll', toggleBackToTopButton);
      toggleBackToTopButton();
   });
  </script>
<script src="assets/js/swiper-bundle.min.js"></script>
<!--=============== MAIN JS ===============-->
<script src="assets/js/main.js"></script>
   </body>
</html>

