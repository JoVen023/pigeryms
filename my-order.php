    <?php
  
    include('includes/config.php');
    date_default_timezone_set('Asia/Manila');
    if (!isset($_SESSION['customer']) || strlen($_SESSION['customer']) == 0) {
        header('location:index.php');
        exit;
    } 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];
    
        // Validate the order ID to prevent SQL injection and unauthorized access
        if (!is_numeric($orderId)) {
            die("Invalid order ID");
        }
    
        $useremail = $_SESSION['customer'];
    
        // Check if the order belongs to the logged-in customer
        $sql = "SELECT cust_id FROM tblorders WHERE id = :orderId";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($order['cust_id'] === $useremail) {
            try {
             
                    // Retrieve the order details
                    $sqlOrderDetails = "SELECT pig_id, name, piglet FROM tblorderdetails WHERE order_id = :orderId";
                    $stmtOrderDetails = $dbh->prepare($sqlOrderDetails);
                    $stmtOrderDetails->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                    $stmtOrderDetails->execute();
                    $orderDetails = $stmtOrderDetails->fetchAll(PDO::FETCH_ASSOC);
                
                    foreach ($orderDetails as $orderDetail) {
                        if ($orderDetail['piglet'] == 1) {
                            $sqlUpdateStatus = "UPDATE tblpiglet_for_sale_details 
                                                SET status = :status 
                                                WHERE id = :id";
                            $stmtUpdateStatus = $dbh->prepare($sqlUpdateStatus);
                            $stmtUpdateStatus->bindValue(':status', "AVAILABLE"); 
                            $stmtUpdateStatus->bindValue(':id', $orderDetail['pig_id'], PDO::PARAM_INT);
                            $stmtUpdateStatus->execute();
                        } else {
                            $sqlUpdateStatus = "UPDATE tblpigforsale 
                                                SET status = :status 
                                                WHERE name = :name";
                            $stmtUpdateStatus = $dbh->prepare($sqlUpdateStatus);
                            $stmtUpdateStatus->bindValue(':status', NULL, PDO::PARAM_NULL); 
                            $stmtUpdateStatus->bindValue(':name', $orderDetail['name']);
                            $stmtUpdateStatus->execute();
                        }
                    }

                $sqlDeleteOrder = "DELETE FROM tblorders WHERE id = :orderId";
                $stmtDeleteOrder = $dbh->prepare($sqlDeleteOrder);
                $stmtDeleteOrder->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $stmtDeleteOrder->execute();
    
                // Use prepared statements for deleting rows from the "tblorderdetails" table
                $sqlDeleteOrderDetails = "DELETE FROM tblorderdetails WHERE order_id = :orderId";
                $stmtDeleteOrderDetails = $dbh->prepare($sqlDeleteOrderDetails);
                $stmtDeleteOrderDetails->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $stmtDeleteOrderDetails->execute();
    
                // Redirect to the same page to update the display after cancellation
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } catch (PDOException $e) {
                // Handle the exception if something goes wrong with the database operation
                die("Error: Unable to cancel order");
            }
        } else {
            die("Unauthorized access"); // If the order doesn't belong to the logged-in customer
        }
    }
        
        // Fetch data from the "orders" table
        $useremail = $_SESSION['customer'];
        $sql = "SELECT  * FROM tblorders
                WHERE cust_id=:useremail";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':useremail', $useremail, PDO::PARAM_STR);
        $stmt->execute();
        $ordersWithDetails = array(); // Array to hold combined data from both tables

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $orderId = $row['id'];

            // Fetch data from the "order_details" table using the order ID
            $sqlOrderDetails = "SELECT * FROM tblorderdetails WHERE order_id = :orderId";
            $stmtOrderDetails = $dbh->prepare($sqlOrderDetails);
            $stmtOrderDetails->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $stmtOrderDetails->execute();

            // Create an array to hold order details for each order
            $orderDetails = array();
            while ($rowOrderDetails = $stmtOrderDetails->fetch(PDO::FETCH_ASSOC)) {
                $orderDetails[] = $rowOrderDetails;
            }

            // Combine the order data and its details into a single array
            $row['order_details'] = $orderDetails;

            // Add the combined array to the main array
            $ordersWithDetails[] = $row;
        }

        // Now $ordersWithDetails will contain the combined data from both tables
        // You can loop through it to display the data as needed.


    // The rest of your HTML code remains the same
    


    ?>

    <!DOCTYPE HTML>
    <html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title>Ronald's Baboyan | My Orders</title>
    <!--Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
    <!--Custome Style -->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    </head>
    <body>
            
    <!--Header-->
    <?php include('includes/header.php');?>
    <!-- /Header --> 
    <!--Page Header-->
    <main>
    <section class="page-header profile_page">
    <div class="container">
        <div class="page-header_wrap">
        <div class="page-heading">
            <h1>My Order</h1>
        </div>
        <ul class="coustom-breadcrumb">
            <li><a href="index.php">Home</a></li>
            <li>My Order</li>
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
        <div class="profile_wrap">
        <!-- Loop through orders and display the order info and its details -->
        <?php foreach ($ordersWithDetails as $order) {
             $statusClass = '';
             if ($order['orderstatus'] == 'Completed') {
                 $statusClass = 'Completed';
             }
            $dateFromDb = $order['orderdate']; // This is the date from the database
            $timestamp = strtotime($dateFromDb); // Convert the date into a Unix timestamp
            $formattedDate = date("F j, Y g:i a", $timestamp); // Format the date
            
            $cancelationTime = strtotime($order['canceltime']);
            // Convert the date into a Unix timestamp
            $formattedDates = date("F j, Y g:i a", $cancelationTime); // Format the date
            $currentTime = time();

            
            echo $formattedDate;
            


             ?>
             
            <div class="order-card">
                <div class="order-info">
                    <div class="h">
                    <h4>Order ID: <?php echo $order['id']; ?> </h4>
                    <h5  class="<?php echo $statusClass; ?>" ><?php echo $order['orderstatus']; ?></h5>

        </div>
                    <p>Ordered Date: <?php echo $formattedDate; ?></p>
                    <p>Type Of Payment: <?php echo $order['mop']; ?></p>
                    <p>Total Amount: <span>&#8369;</span><?php echo $order['total_amount']; ?></p>
                </div>

                <div class="order-info">
                    <h3>Order Details</h3>
                    <table>
                        <thead class="orders">
                            <tr class="<?php echo $order['orderstatus']; ?>">
                                <th>Pig Name</th>
                                <th>Weight Class</th>
                                <th>Price</th>
                                <th>Age</th>
                                <th>Sex</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through order details for this order -->
                            <?php foreach ($order['order_details'] as $orderDetail) { ?>
                                <tr>
                                    <td><?php echo $orderDetail['name']; ?></td>
                                    <td><?php echo $orderDetail['weight_class']; ?></td>
                                    <td><?php echo $orderDetail['price'];?>/kg</td>
                                    <td><?php echo $orderDetail['age']; ?></td>
                                    <td><?php echo $orderDetail['sex']; ?></td>
                                    <td><?php echo $orderDetail['quantity']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" <?php if($currentTime > $cancelationTime || $order['orderstatus'] == "Completed"): echo 'style="display:none"'; endif; ?>>
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <div class="order-actions">

            <button type="button" id="cancelButton-<?php echo $order['id']; ?>" class="cancel-btn" data-toggle="modal" data-target="#cancelModal-<?php echo $order['id']; ?>">
    Cancel Order
</button>
<br>
    <div class="endtime"><p>You can cancel this order until <?php echo $formattedDates ?></p></div>
   
     <!-- Display end time here -->
            </div>
        </form>

    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelModal-<?php echo $order['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel-<?php echo $order['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                <div class="text-center">
                    <img src="img/cancel.svg" alt="Profile Picture" style="width: 150px; height: 150px;">
                    <h3 style="margin-top: 10px; font-weight: bold;color:#000">Are you sure you want to cancel this order?</h3>
                  </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color:#000;">Close</button>
                    <button type="button" class="btn btn-danger" onclick="cancelOrder('<?php echo $order['id']; ?>')" style="background-color:#FF0000;">Confirm</button>
                </div>
            </div>
        </div>
    </div>
        <?php } ?>
    </div>
            
    </div>
        </div>
        </div>
    </div>
                </div>

    <!-- The Modal -->
    </section>
                            </main>
  
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
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

   

   

    
    <!-- Scripts --> 
    <script>
function cancelOrder(orderId) {
    document.querySelector("form [name='order_id']").value = orderId;
    document.querySelector("form").submit();
}

 
    // For each order, calculate and display the end time for order cancellation immediately when the page loads

</script>
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
