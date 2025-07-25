<?php
include('includes/config.php');
error_reporting(0);
if (!isset($_SESSION['customer'])) {
  header("Location: login.php");
  exit();
}


$customerId = $_SESSION['customer'];
$pigId = $_GET['id'];

// Retrieve the pig details from the database using the $pigId
$query = "SELECT * FROM tblpigforsale WHERE id = :pigId";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $pigId, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);

if ($pig) {
    // Get other pigs with the same weight class
    $weightClass = $pig['weight_class'];
    $query = "SELECT * FROM tblpigforsale WHERE weight_class = :weightClass AND id != :pigId";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':weightClass', $weightClass, PDO::PARAM_STR);
    $stmt->bindParam(':pigId', $pigId, PDO::PARAM_INT);
    $stmt->execute();
    $similarPigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['placeorder'])) {
  $mop = $_POST['mop'];
  // Insert the order into tblorders table
  $stmt = $dbh->prepare("INSERT INTO tblorders (cust_id, mop,orderstatus,canceltime ) VALUES (:cust, :mop, :status,DATE_ADD(CURRENT_TIMESTAMP(6), INTERVAL 8 HOUR))");
  $stmt->bindParam(':cust', $customerId, PDO::PARAM_INT);
  $stmt->bindParam(':mop', $mop, PDO::PARAM_STR);
  $stmt->bindValue(':status', 'Pending');

  $stmt->execute();
  $order_id = $dbh->lastInsertId();

  // Insert the order details into tblorderdetails table
  $pig_name = $_POST['pig_name'];
  $pig_id = $_POST['pig_id'];
  $sow_id = $_POST['sow_id'];
  $pig_price = $_POST['pig_price'];
  $quantity = $_POST['quantity'];
  $weight = $_POST['weight'];
  $sex = $_POST['sex'];
  $age = $_POST['age'];


  $stmt = $dbh->prepare("INSERT INTO tblorderdetails (order_id,pig_id,sow_id, name, price, quantity, weight_class, sex, age) 
                          VALUES (:order_id,:pig_id,:sow_id, :pig_name, :pig_price, :quantity, :weight, :sex, :age)");
  $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
  $stmt->bindParam(':pig_id', $pig_id, PDO::PARAM_INT);
  $stmt->bindParam(':pig_name', $pig_name, PDO::PARAM_STR);
  $stmt->bindParam(':sow_id', $sow_id, PDO::PARAM_INT);
  $stmt->bindParam(':pig_price', $pig_price, PDO::PARAM_STR);
  $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
  $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
  $stmt->bindParam(':age', $age, PDO::PARAM_STR);
  $stmt->execute();

     // Update status of the ordered item
     $updateStatus = $dbh->prepare("UPDATE tblpigforsale SET status = :status WHERE name = :name");
     $updateStatus->bindValue(':status', 'ordered');
     $updateStatus->bindValue(':name', $pig_name, PDO::PARAM_STR);
     $updateStatus->execute();

  if ($stmt->rowCount() > 0) {
      // The insert was successful, so output a JavaScript alert
      echo "<script type='text/javascript'>alert('Order Successfully'); window.location.href = 'my-order.php';</script>";
  } else {
      // The insert failed, so output an error message
      echo "Error: Order placement failed.";
  }

  exit;
}
?>  

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title>SHOP</title>
    
    <!-- Core Libraries: Bootstrap and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <!-- Other libraries and styles: Order them based on your need -->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

    <link href="assets/css/slick.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/logos.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<body>

    <!--Header-->
    <?php include('includes/header.php'); ?>
    <!-- /Header -->

    <!--Listing-->
    <main>
           
  <section class="pd" style="padding-top: 10vh">
  <?php if ($pig): ?>
  <div class="details">
  <div class="image-columns">
  <div id="pigCarousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <!-- The main image (default view) -->
        <div class="item active">
            <img src="admin/img/<?php echo $pig['img']; ?>" alt="<?php echo $pig['name']; ?>">
        </div>
        <!-- Additional images (different angles) -->
        <div class="item">
            <img src="admin/img/<?php echo $pig['back']; ?>" alt="Second angle view">
        </div>
        <div class="item">
            <img src="admin/img/<?php echo $pig['side']; ?>" alt="Second angle view">
        </div>
        <div class="item">
            <img src="admin/img/<?php echo $pig['front']; ?>" alt="Second angle view">
        </div>
        <!-- You can add more items (images) here... -->
    </div>
    <!-- Carousel Controls (Optional) -->
    <a class="left carousel-control" href="#pigCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#pigCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
  </div>

  <div class="text-columns">
  <h1><?php echo $pig['name']; ?> (<?php echo $pig['weight_class']; ?>)</h1>
                        <p> &#8369;<?php echo $pig['price']; ?>/kg</p>
                        <p>Age: <?php echo $pig['age']; ?></p>
                        <p>Sex: <?php echo $pig['sex']; ?></p>
    <form action="cart.php" method="post">
                        <p>Quantity<p>
    <div class="quantity-input">
      <p>1</p>
      <input type="hidden" class="quantity" name="quantity" id="quantityInput" value="1" min="1" max="50" placeholder="Quantity" required>

    </div>
  </p>
                         <input type="hidden" name="pig_id" value="<?=$pig['id']?>">
                   <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                   <button type="button" class="order-btn" data-toggle="modal" data-target="#confirmModal" id="openModalBtn">Order</button>
  </form>

  <form id="cartForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <!-- Rest of your form content -->
                <!-- Add this modal to your HTML -->
                <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="text-center">
                    <img src="img/order.svg" alt="Profile Picture" width="150px" height="150px">
                    <h3>Order Details</h3>
                  </div>
            </div>
                            <div class="modal-body">
                                <h6>Type Of Payment: <select name="mop" id="statusSelect" class="form-control" style="width:15rem;" required>

</select>
  </h6>
                                <table>
                                    <!-- Display cart details here -->
                                    <thead>
                                        <tr>
                                            <th>Pig</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                            <tr>
                                                <td><?=$pig['name']?></td>
                                                <input type="hidden" name="pig_id" value="<?=$pig['id']?>">
                                                <input type="hidden" name="pig_name" value="<?=$pig['name']?>">
                                                <input type="hidden" name="sow_id" value="<?=$pig['sow_id']?>">
                                                <td><span>&#8369;</span><?=$pig['price']?>/kg</td>
                                                <input type="hidden" name="pig_price" value="<?=$pig['price']?>">
                                                <td>
                                                <p style="color:black;">1</p>
                                              </td>
                                              <input type="hidden" name="quantity" id="modalQuantityInput" value="1">
                                                <td><?=$pig['weight_class']?></td>
                                                <input type="hidden" name="weight" value="<?=$pig['weight_class']?>">
                                                
                                                <input type="hidden" name="sex" value="<?=$pig['sex']?>">
                                                <input type="hidden" name="age" value="<?=$pig['age']?>">
                                                <input type="hidden" id="date" name="orderdate" value="$customerId">
                                              
                                            </tr>
                                        
                                    </tbody>
                                </table>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondarys" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="confirmOrderBtn" name="placeorder">Confirm Order</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
  </div>


</div>

<?php else: ?>
            <div class="container">
                <p>Pig not found.</p>
            </div>
        <?php endif; ?>
  </section>



  </main>
  <?php if (!empty($similarPigs)): ?>
    <section class="listing-pages">

    <div class="cons">
        <div class="header">
            <h3 class="h">YOU MAY ALSO LIKE</h3>
        </div>
        <br>
        <div style="clear: both;"></div>
        <ul class="pigs" id="pigsList">
            <?php foreach ($similarPigs as $similarPig): ?>
                <li class="pigcard">
                    <h3 class="weightclass"><?php echo $similarPig['weight_class']; ?></h3>
                    <img src="admin/img/<?php echo $similarPig['img']; ?>" alt="<?php echo $similarPig['name']; ?>">
                    <p class="name"><?php echo $similarPig['name']; ?></p>
                    <p class="price">&#8369;<?php echo $similarPig['price']; ?>/kg</p>
                    <a href="pig_details.php?id=<?php echo $similarPig['id']; ?>" class="view-btn">View</a>
                </li>
            <?php endforeach; ?>
        </ul>
            </div>
           
    </section>
    <?php endif; ?>
    <!-- /Listing -->

    <!--Footer -->
    <?php include('includes/footerhome.php'); ?>
    <!-- /Footer -->

    <!--Back to top-->
    <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
    <!--/Back to top-->
<!-- Scripts -->
<script>
$(document).ready(function() {
    $('#statusSelect').select2({
        minimumResultsForSearch: Infinity,
        data: [
            { id: '', text: 'Select', selected: true , color: '#000'},
            { id: 'Cash', text: '<i class="bi bi-cash" style="color: green;"></i> Cash', color: '#000' },
            { id: 'GCash', text: '<img src="img/gcash.svg" width="20" height="20" alt="Gcash"> &nbsp; GCash', color: '#000' },
            { id: 'BankCheck', text: '<i class="bi bi-bank" style="color: blue;"></i> Bank Check' , color: '#000'}
        ],
        escapeMarkup: function(markup) {
            return markup;  // Do not escape the provided HTML
        },
        templateResult: function(data) {
            if (!data.id) { return data.text; }
            var $result = $(
                '<span style="color:' + data.color + '">' + data.text + '</span>'
            );
            return $result;
        },
    });

    let currentIndex = 0;

function moveSlide() {
  const slides = document.querySelectorAll('.carousel-item');
  const container = document.querySelector('.carousel-inner');

  // Calculate new position
  const newLeft = -currentIndex * 300 + 'px';  // The same width as the carousel
  container.style.left = newLeft;
}

function prevSlide() {
  const slides = document.querySelectorAll('.carousel-item');
  
  if (currentIndex > 0) {
    currentIndex--;
    moveSlide();
  }
}

function nextSlide() {
  const slides = document.querySelectorAll('.carousel-item');
  
  if (currentIndex < slides.length - 1) {
    currentIndex++;
    moveSlide();
  }
}

document.querySelector('.carousel-control-prev').addEventListener('click', prevSlide);
document.querySelector('.carousel-control-next').addEventListener('click', nextSlide);

});

</script>


<script src="assets/js/bootstrap.min.js"></script>

<!--Switcher-->
<!--bootstrap-slider-JS-->
<script src="assets/js/bootstrap-slider.min.js"></script>

</body>

</html>
