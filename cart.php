<?php

include('includes/config.php');

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit();
}
$customerId = $_SESSION['customer'];
if (isset($_POST['empty'])) {
    // Clear the cart by unsetting the session variable
    $products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    $products = array();
    // If there are products in cart
    if ($products_in_cart) {
        // There are products in the cart so we need to select those products from the database
        // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
        $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
        $stmt = $dbh->prepare('SELECT * FROM tblpigforsale WHERE id IN (' . $array_to_question_marks . ')');
        // We only need the array keys, not the values, the keys are the id's of the products
        $stmt->execute(array_keys($products_in_cart));
        // Fetch the products from the database and return the result as an Array
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Calculate the subtotal
        $query = "UPDATE tblpigforsale SET status = :status WHERE name = :name";
       
        $stmt = $dbh->prepare($query);

        foreach ($products as $pig) {
            $stmt->bindValue(':status', NULL);
            $stmt->bindValue(':name', $pig['name']);
            $stmt->execute();
            // Update the quantity in the session cart
        }
        unset($_SESSION['cart']);
  
} 
    // Redirect to the cart page to display the empty cart
    header('Location: cart.php');
    exit;
}
// If the user clicked the add to cart button on the product page we can check for the form data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pig_id'], $_POST['quantity']) && is_numeric($_POST['pig_id']) && is_numeric($_POST['quantity'])) {
    // Set the post variables so we easily identify them, also make sure they are integer
   
    $pig_id = (int)$_POST['pig_id'];
    $quantity = (int)$_POST['quantity'];
    // Prepare the SQL statement, we basically are checking if the product exists in our database
    $updateStatus = $dbh->prepare("UPDATE tblpigforsale SET status = :status WHERE id = :id");
    $updateStatus->bindValue(':status', 'ordered');
    $updateStatus->bindValue(':id', $pig_id);
    $updateStatus->execute();

    $stmt = $dbh->prepare('SELECT * FROM tblpigforsale WHERE id = ?');
    $stmt->execute([$_POST['pig_id']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the product exists (array is not empty)
    if ($product && $quantity > 0) {
        // Product exists in database, now we can create/update the session variable for the cart
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
      
            // Product is not in cart so add it
            $_SESSION['cart'][$pig_id] = $quantity;
        
    } else {
        // There are no products in cart, this will add the first product to cart
        $_SESSION['cart'] = array($pig_id => $quantity);
    }
}
    
    // Prevent form resubmission...
    header('location: cart.php');
    exit;
}

// Remove product from cart, check for the URL param "remove", this is the product id, make sure it's a number and check if it's in the cart
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    // Remove the product from the shopping cart

    $products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    $products = array();
    // If there are products in cart
    if ($products_in_cart) {
        // There are products in the cart so we need to select those products from the database
        // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
        $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
        $stmt = $dbh->prepare('SELECT * FROM tblpigforsale WHERE id IN (' . $array_to_question_marks . ')');
        // We only need the array keys, not the values, the keys are the id's of the products
        $stmt->execute(array_keys($products_in_cart));
        // Fetch the products from the database and return the result as an Array
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Calculate the subtotal
        $query = "UPDATE tblpigforsale SET status = :status WHERE name = :name";
       
        $stmt = $dbh->prepare($query);

        foreach ($products as $pig) {
            $stmt->bindValue(':status', NULL);
            $stmt->bindValue(':name', $pig['name']);
            $stmt->execute();
            // Update the quantity in the session cart
        }
    unset($_SESSION['cart'][$_GET['remove']]);
  
}
}

// Send the user to the place order page if they click the Place Order button, also the cart should not be empty
if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $mop = $_POST['mop'];
    $products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    $products = array();
    $stmt = $dbh->prepare("INSERT INTO tblorders(cust_id,mop,orderstatus,canceltime) VALUES (:cust,:mop,:status,DATE_ADD(CURRENT_TIMESTAMP(6), INTERVAL 8 HOUR))");
    $stmt->bindParam(':cust', $customerId);
    $stmt->bindParam(':mop', $mop);
    $stmt->bindValue(':status', 'Pending');
    $stmt->execute();
    $order_id = $dbh->lastInsertId();

    // If there are products in cart
    if ($products_in_cart) {
        // There are products in the cart so we need to select those products from the database
        // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
        $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
        $stmt = $dbh->prepare('SELECT * FROM tblpigforsale WHERE id IN (' . $array_to_question_marks . ')');
        // We only need the array keys, not the values, the keys are the id's of the products
        $stmt->execute(array_keys($products_in_cart));
        // Fetch the products from the database and return the result as an Array
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Calculate the subtotal
        $query = "INSERT INTO tblorderdetails(`order_id`,`pig_id`,`name`,`sow_id`,`sex`,`age`, `price`,`quantity`,`weight_class`) VALUES (:order_id,:pig_id,:pig_name, :sow_id,:sex,:age,:pig_price, :pig_qty ,:weight)";

        $stmt = $dbh->prepare($query);

        foreach ($products as $pig) {
            $stmt->bindValue(':order_id', $order_id);
            $stmt->bindValue(':pig_id', $pig['id']);
            $stmt->bindValue(':pig_name', $pig['name']);
            $stmt->bindValue(':sow_id', $pig['sow_id']);
            $stmt->bindValue(':sex', $pig['sex']);
            $stmt->bindValue(':age', $pig['age']);
            $stmt->bindValue(':pig_qty', $products_in_cart[$pig['id']]);
            $stmt->bindValue(':pig_price', $pig['price']);
            $stmt->bindValue(':weight', $pig['weight_class']);
            $stmt->execute();
            // Update the quantity in the session cart
        }

        unset($_SESSION['cart']);
    }

    if ($stmt->rowCount() > 0) {
        // The insert was successful, so output a JavaScript alert
        echo "<script type='text/javascript'>alert('Order Successfully'); window.location.href = 'my-order.php';</script>";
    } else {
        // The insert failed, so output an error message
        echo "Error: " . $dbh->errorCode();
    }

    exit;
}

// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();

// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $dbh->prepare('SELECT * FROM tblpigforsale WHERE id IN (' . $array_to_question_marks . ')');
    // We only need the array keys, not the values, the keys are the id's of the products
    $stmt->execute(array_keys($products_in_cart));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Calculate the subtotal
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title> Ronald's Baboyan | My Orders</title>
    <!--Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <!--Custome Style -->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!--OWL Carousel slider-->
    <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
    <!--slick-slider -->
    <link href="assets/css/slick.css" rel="stylesheet">
    <!--bootstrap-slider -->
    <link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
    <!--FontAwesome Font Style -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/logos.jpeg">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    </head>
<body>
    <!--HEADER-->
    <?php
    include 'includes/header.php';
    ?>
    <!--STICKY TOP END-->

    <main class="main">
        <!-- Demo Content -->
        <div class="cart content-wrapper">
            <h2 class="head" >Shopping Cart</h2>
             <form id="quantity-form-<?=$pig['id']?>" action="cart.php" method="post">
                <table>
                    <thead>
                        <tr>
                            <td class="twopig"colspan="2">Pig</td>
                            <td>Price</td>
                            <td>Quantity</td>
                            <td>Weight</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): 
                            ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">There are currently no items in your Shopping Cart. <a href="pig-list.php"style="color:rgb(20, 150, 15); ">Shop Now</a></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $pig): ?>
                                <tr data-pig-id="<?=$pig['id']?>">
                                   
                                        <td class="img">
                                            <a href="pig_details.php?id=<?=$pig['id']?>">
                                                <img src="admin/img/<?=$pig['img']?>" width="50" height="50" alt="<?=$pig['name']?> (<?=$pig['weight_class']?>)kg">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="pig_details.php?id=<?=$pig['id']?>"><?=$pig['name']?></a>
                                        </td>
                                        
                                        <td class="price"><span>&#8369;</span><?=$pig['price']?>/kg</td>
                                       
                                      
                                        <td class="quantity-container">
                                                   
                                        <?=$products_in_cart[$pig['id']]?>
                            </div>
                           
                                        </td>
                                        <td class="weight_class"><?=$pig['weight_class']?></td>
                                        <td>
                                            <a href="cart.php?remove=<?=$pig['id']?>" class="remove">Remove</a>
                                        </td>
                                </tr>
                               
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="button-container" <?php if (empty($products)) { echo 'style="display: none;"'; } ?>>
                <div class="button" >
               
    <input type="button" value="Place Order" data-toggle="modal" data-target="#confirmModal">

                </div>
                <div class="button"><input type="submit" value="Empty Cart" name="empty"></div>
                </div>
            </form>
            <form id="cartForm" action="<?=$_SERVER['PHP_SELF']?>" method="post">
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
                                        <?php foreach ($products as $pig): ?>
                                            <tr>
                                                <td><?=$pig['name']?></td>
                                                <input type="hidden" name="pig_name" value="<?=$pig['name']?>">
                                                <td><span>&#8369;</span><?=$pig['price']?>/kg</td>
                                                <input type="hidden" name="pig_price" value="<?=$pig['price']?>">

                                                <td>
  <span class="modal-quantity" data-pig-id="<?=$pig['id']?>"> <?=$products_in_cart[$pig['id']]?></span>
  <input type="hidden" name="quantities[<?=$pig['id']?>]" value="<?=$products_in_cart[$pig['id']]?>">
</td>
                                                <td><?=$pig['weight_class']?></td>
                                                <input type="hidden" name="weight" value="<?=$pig['weight_class']?>">
                                                
                                                <input type="hidden" name="sex" value="<?=$pig['sex']?>">
                                                <input type="hidden" name="age" value="<?=$pig['age']?>">
                                           
                                            </tr>
                                        <?php endforeach; ?>
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
    </main>

    <!-----footer---->
    <?php
    include 'includes/footerhome.php';
    ?>
    <!----- end footer---->
    <!-- Scripts -->
    <script>
$(document).ready(function() {


  $('#cartForm').on('submit', function(e) {
    updateModalQuantities();
  });

  $('.button-container .button input[type="button"]').click(function() {
    updateModalQuantities();
  });

  function updateModalQuantities() {
    $('[data-pig-id]').each(function() {
      var pigId = $(this).data('pig-id');
      var quantity = $('#quantity-' + pigId).val();
      var modalQuantityEl = $('.modal-quantity[data-pig-id="' + pigId + '"]');
      if (modalQuantityEl.length) {
        modalQuantityEl.text(quantity);
        // Also update the quantity field in the form to be submitted
        $('input[name="quantities[' + pigId + ']"]').val(quantity);
      }
    });
  }

    $('#statusSelect').select2({
        minimumResultsForSearch: Infinity,
        data: [
            { id: '', text: 'Select', selected: true , color: '#000'},
            { id: 'Cash', text: '<i class="bi bi-cash" style="color: green;"></i>Cash', color: '#000' },
            { id: 'GCash', text: '<img src="img/gcash.svg" width="20" height="20" alt="Gcash"> &nbsp;  GCash', color: '#000' },
            { id: 'BankCheck', text: '<i class="bi bi-bank" style="color: blue;"></i>Bank Check' , color: '#000'}
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
});
</script>

    <script src="assets/js/bootstrap.min.js"></script> 
    <script src="assets/js/interface.js"></script> 
    <!--Switcher-->

    <!--bootstrap-slider-JS--> 
    <script src="assets/js/bootstrap-slider.min.js"></script> 
    <!--Slider-JS--> 

    <script src="assets/js/owl.carousel.min.js"></script>
</body>
</html>