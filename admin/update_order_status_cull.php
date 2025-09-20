<?php

include('includes/config.php');

if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
    exit;
}

if(isset($_POST['sowIds']) && is_array($_POST['sowIds'])) {
    foreach($_POST['sowIds'] as $sowId => $count) {
        $sql = "UPDATE tblgrowingphase SET pigs = pigs - :count WHERE id = :sowId";
        $query1 = $dbh->prepare($sql);
        $query1->bindParam(':count', $count, PDO::PARAM_INT);
        $query1->bindParam(':sowId', $sowId, PDO::PARAM_INT);
        $query1->execute();
    }
}

    if ($_POST['orderId'] && $_POST['status']) {
        $orderId = $_POST['orderId'];
        $status = $_POST['status'];
        $date = $_POST['date'];
        $totalPrice = $_POST['totalPrice'];
        // This will hold the weights for each order detail
        $weights = 0;

            $pigletquer = $dbh->prepare("SELECT pig_id AS piglets_id FROM tblorderdetails WHERE order_id = :orderid ");
             $pigletquer->bindParam(':orderid',$orderId,PDO::PARAM_INT);
             $pigletquer->execute();
             $piglets_id = $pigletquer->fetchAll(PDO::FETCH_ASSOC);


             foreach ($piglets_id as $id) {
                $pigletid = $id['piglets_id'];
                $sql2 = $dbh->prepare("UPDATE tblculling SET status = 'Sold' WHERE id = :pigletid");
                $sql2->bindParam(':pigletid', $pigletid, PDO::PARAM_INT);
                $sql2->execute();
            }
        

        // Update order status
        $sql ="UPDATE tblorders SET orderstatus=:status, deliverydate =:date WHERE id=:orderId";
    $query = $dbh -> prepare($sql);
    $query -> bindParam(':status', $status, PDO::PARAM_STR);
    $query -> bindParam(':date', $date, PDO::PARAM_STR);
    $query -> bindParam(':orderId', $orderId, PDO::PARAM_STR);

   

        try {
            $query->execute();
        
        } catch(PDOException $e) {
            echo "Query failed: " . $e->getMessage();
            exit;
        }
        $price ="SELECT total_amount FROM tblorders WHERE id=:orderId";
    $queryprice = $dbh -> prepare($price);
    $queryprice -> bindParam(':orderId', $orderId, PDO::PARAM_STR);
        try {
            $queryprice->execute();
            $pricerow = $queryprice->fetch(PDO::FETCH_ASSOC);
            if ($pricerow){
                $currprice = $pricerow['total_amount'];
            }else{
                die("Error Found");
            }
         
        } catch(PDOException $e) {
            echo "Query failed: " . $e->getMessage();
            exit;
        }


        $sql4 = "UPDATE tblsales SET total_sales = total_sales + :totalprice WHERE id = 1";
        $query4 = $dbh->prepare($sql4);
        $query4->bindParam(':totalprice', $currprice, PDO::PARAM_INT);
        
        try {
            $query4->execute();
          
        } catch(PDOException $e) {  
            echo "Query failed: " . $e->getMessage();
            exit;
        }
    

        // // Update the weights for each order detail
        // foreach ($weights as $detailId => $weight) {
        //     $sql = "UPDATE tblorderdetails SET weight=:weight WHERE id=:detailId AND order_id=:orderId";
        //     $query = $dbh -> prepare($sql);
        //     $query -> bindParam(':weight', $weight, PDO::PARAM_STR);
        //     $query -> bindParam(':detailId', $detailId, PDO::PARAM_STR);
        //     $query -> bindParam(':orderId', $orderId, PDO::PARAM_STR);
        //     $query -> execute();

            
            
        // }

        echo "Order status updated successfully";
    }
    

?>
