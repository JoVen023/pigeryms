<?php
include('includes/config.php');
error_reporting(0);
if (!isset($_SESSION['customer'])) {
  header("Location: login.php");
  exit();
}

$customerId = $_SESSION['customer'];
$pigId = $_GET['id'];

$query = "SELECT tfs.*,tfsd.*,tfs.id AS id, tg.img AS group_image,tg.sow_id AS sow_id, sum(tfsd.price) AS price,count(tfsd.id) AS total_piglets 
FROM tblpiglet_for_sale tfs 
INNER JOIN tblpiglet_for_sale_details tfsd ON tfs.id = tfsd.tblpiglet_for_sale_id
LEFT JOIN tblgrowingphase tg ON tg.id =tfs.growingphase_id 
WHERE tfs.id =:pigId";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':pigId', $pigId, PDO::PARAM_INT);
$stmt->execute();
$pig = $stmt->fetch(PDO::FETCH_ASSOC);

$farrowed = $pig['farrowed_Date'];
$formattedDate = date("F j, Y", strtotime($farrowed));


// age
$weaningDate = new DateTime($pig['farrowed_Date']);
$currentDate = new DateTime();  
$weaningDate->setTime(0, 0, 0);
$currentDate->setTime(0, 0, 0);
$interval = $currentDate->diff($weaningDate);

$daysDifference = $interval->days;
$age = $daysDifference;
// age

$sow_id = $pig['sow_id'];

if ($pig) {
    $piglet_id = $pig['id'];
    $query = "SELECT * FROM tblpiglet_for_sale_details WHERE tblpiglet_for_sale_id = :piglet_id AND status = 'AVAILABLE'";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':piglet_id', $piglet_id, PDO::PARAM_STR);
    $stmt->execute();
    $similarPigs = $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

if (isset($_POST['placeorder'])) {
    $pig_name   = $_POST['pig_name'];
    $pig_id     = $_POST['pig_id'];
    $sow_id     = $_POST['sow_id'];
    $pig_price  = $_POST['pig_price'];
    $quantity   = $_POST['quantity'];
    $weight     = $_POST['weight'];
    $gender     = $_POST['gender']; 
    $age        = $_POST['age'];
    $mop        = $_POST['mop'];
    $orderdate  = $_POST['orderdate'];

    
    $stmt = $dbh->prepare("INSERT INTO tblorders 
    (cust_id, mop, total_amount, orderstatus, orderdate, canceltime, piglets) 
    VALUES (:cust, :mop, :total_amount, :status, :orderdate, DATE_ADD(CURRENT_TIMESTAMP(6), INTERVAL 8 HOUR), 1)");

$stmt->bindParam(':cust', $customerId, PDO::PARAM_INT);
$stmt->bindParam(':mop', $mop, PDO::PARAM_STR);
$stmt->bindParam(':total_amount', $pig_price, PDO::PARAM_INT);
$stmt->bindValue(':status', 'Pending');
$stmt->bindParam(':orderdate', $orderdate, PDO::PARAM_STR);

$stmt->execute();
$order_id = $dbh->lastInsertId();

// Insert into tblorderdetails
    $stmt = $dbh->prepare("INSERT INTO tblorderdetails 
        (order_id, pig_id, sow_id, name, price, quantity, weight_class, sex, age, piglet) 
        VALUES (:order_id, :pig_id, :sow_id, :pig_name, :pig_price, :quantity, :weight, :sex, :age, 1)");

    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':pig_id', $pig_id, PDO::PARAM_INT);
    $stmt->bindParam(':sow_id', $sow_id, PDO::PARAM_INT);
    $stmt->bindParam(':pig_name', $pig_name, PDO::PARAM_STR);
    $stmt->bindParam(':pig_price', $pig_price, PDO::PARAM_STR);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
    $stmt->bindParam(':sex', $gender, PDO::PARAM_STR);
    $stmt->bindParam(':age', $age, PDO::PARAM_STR);
    $stmt->execute();

    // Update piglet status
    $updateStatus = $dbh->prepare("UPDATE tblpiglet_for_sale_details 
        SET status = :status WHERE id = :pig_id");
    $updateStatus->bindValue(':status', 'ordered');
    $updateStatus->bindValue(':pig_id', $pig_id, PDO::PARAM_STR);
    $updateStatus->execute();

    if ($stmt->rowCount() > 0) {
        echo "<script type='text/javascript'>
                alert('Order Successfully'); 
                window.location.href = 'my-order.php';
              </script>";
    } else {
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
            <img src="admin/img/img_piglets_for_sale/<?php echo $pig['img']; ?>" width="200" height="200" alt="<?php echo $pig['name']; ?>">
        </div>
    </div>
</div>
  </div>

  <div class="text-columns">
  <h1><?php echo $pig['name']; ?> </h1>
                        <p>Age: <?php echo $age; ?></p>
                        <p>Weaned Date: <?php echo $formattedDate ; ?></p>
    <form action="cart.php" method="post">
                        <p>Quantity<p>
    <div class="quantity-input">
      <p><?=$pig['total_piglets'];?></p>
      <input type="hidden" class="quantity" name="quantity" id="quantityInput" value="1" min="1" max="50" placeholder="Quantity" required>

    </div>
  </p>
                         <input type="hidden" name="pig_id" value="<?=$pig['id']?>">
  </form>



  <form id="cartForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <!-- Rest of your form content -->
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

          <!-- ✅ Your original Mode of Payment code -->
          <h6>Type Of Payment: <select name="mop" id="statusSelect" class="form-control" style="width:15rem;" required> </select> </h6>

          <br>
          <br>
          <table class="table">
            <thead>
              <tr>
                <th>Pig</th>
                <th>Gender</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Weight</th>
              </tr>
            </thead>
            <tbody>
              <tr>
              <td id="modalPigNameTd"></td>
              <td id="modalPigGenderTd"></td>
                <td><span>&#8369;</span><span id="modalPigPrice"></span>/kg</td>
                <td><p style="color:black;">1</p></td>
                <td id="modalPigWeight"></td>
              </tr>
            </tbody>
          </table>
          <!-- Hidden inputs for backend -->
          <input type="hidden" name="pig_id" id="modalPigId">
          <input type="hidden" name="sow_id" id="modalSowId">
          <input type="hidden" name="pig_name" id="modalPigName">
          <input type="hidden" name="pig_price" id="modalPigPriceInput">
          <input type="hidden" name="quantity" value="1">
          <input type="hidden" name="weight" id="modalPigWeightInput">
          <input type="hidden" name="gender" id="modalPigGenderInput">
          <input type="hidden" name="age" id="modalPigAge">
          <input type="hidden" id="date" name="orderdate" value="<?= date('Y-m-d'); ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondarys" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" name="placeorder">Confirm Order</button>
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
            <h3 class="h">Piglets List</h3>
        </div>
        <br>
        <div style="clear: both;"></div>
        <ul class="piglets" id="pigsList">
        <?php foreach ($similarPigs as $similarPig): ?>
    <li class="pigletcard">
        <h3 class="weightclass"><?php echo $similarPig['piglet_weight']; ?> kg</h3>
        <img src="admin/img/<?php echo $similarPig['img']; ?>" alt="<?php echo $similarPig['name']; ?>">
        <p class="name"><?php echo $similarPig['gender']; ?></p>
        <p class="price">&#8369;<?php echo $similarPig['price']; ?></p>
        <!-- <button type="submit" class="add-to-cart-btn">Add to Cart</button> -->
        <button 
        type="button" 
    class="order-btn" 
    data-toggle="modal" 
    data-target="#confirmModal"
    data-id="<?= $similarPig['id']; ?>"
    data-name="<?= $similarPig['id']; ?>"
    data-gender="<?= $similarPig['gender']; ?>"
    data-price="<?= $similarPig['price']; ?>"
    data-age="<?= $age; ?>"
    data-sow_id="<?= $sow_id; ?>"
    data-weight="<?= $similarPig['piglet_weight']; ?>"
    data-img="admin/img/<?= $similarPig['img']; ?>"
        >
            Order
        </button>
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

$('#confirmModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); 

  var id = button.data('id');
  var price = button.data('price');
  var weight = button.data('weight');
  var gender = button.data('gender');
  var img = button.data('img');
  var sow_id = button.data('sow_id');
  var age = button.data('age');
  var name = button.data('name'); 



  // Update modal fields
  var modal = $(this);
  modal.find('#modalPigImg').attr('src', img);
  modal.find('#modalPigid').text(id);
  modal.find('#modalPigNameTd').text(name);
  modal.find('#modalPigPrice').html("₱" + price);
  modal.find('#modalPigWeight').text(weight + " kg");
  modal.find('#modalPigGenderTd').text(gender);

  // Hidden form inputs
  modal.find('#modalPigId').val(id);
  modal.find('#modalSowId').val(sow_id);
  modal.find('#modalPigName').val(name);
  modal.find('#modalPigPriceInput').val(price);
  modal.find('#modalPigWeightInput').val(weight);
  modal.find('#modalPigGenderInput').val(gender);
  modal.find('#modalPigAge').val(age);
});

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
                                                                                                        