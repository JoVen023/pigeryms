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
        $weights = $_POST['weights'];

            $pigletquer = $dbh->prepare("SELECT pg.piglet_id AS piglets_id FROM tblpigforsale pg
             LEFT JOIN tblorderdetails od ON pg.id = od.pig_id WHERE order_id = :orderid ");
             $pigletquer->bindParam(':orderid',$orderId,PDO::PARAM_INT);
             $pigletquer->execute();
             $piglets_id = $pigletquer->fetchAll(PDO::FETCH_ASSOC);


             foreach ($piglets_id as $id) {
                $pigletid = $id['piglets_id'];
                $sql2 = $dbh->prepare("UPDATE piglets SET status = 'Sold' WHERE id = :pigletid");
                $sql2->bindParam(':pigletid', $pigletid, PDO::PARAM_INT);
                $sql2->execute();
            }
        

        // Update order status
        $sql ="UPDATE tblorders SET orderstatus=:status, total_amount=:totalPrice , deliverydate =:date WHERE id=:orderId";
    $query = $dbh -> prepare($sql);
    $query -> bindParam(':status', $status, PDO::PARAM_STR);
    $query -> bindParam(':totalPrice', $totalPrice, PDO::PARAM_INT);
    $query -> bindParam(':date', $date, PDO::PARAM_STR);
    $query -> bindParam(':orderId', $orderId, PDO::PARAM_STR);

   

        try {
            $query->execute();
         
        } catch(PDOException $e) {
            echo "Query failed: " . $e->getMessage();
            exit;
        }

        $sql = "UPDATE tblsales SET total_sales = total_sales + :totalprice WHERE id = 1 " ;
        $query1 = $dbh->prepare($sql);
        $query1->bindParam(':totalprice', $totalPrice, PDO::PARAM_INT);
        
        try {
            $query1->execute();
          
        } catch(PDOException $e) {
            echo "Query failed: " . $e->getMessage();
            exit;
        }
    

        // Update the weights for each order detail
        foreach ($weights as $detailId => $weight) {
            $sql = "UPDATE tblorderdetails SET weight=:weight WHERE id=:detailId AND order_id=:orderId";
            $query = $dbh -> prepare($sql);
            $query -> bindParam(':weight', $weight, PDO::PARAM_STR);
            $query -> bindParam(':detailId', $detailId, PDO::PARAM_STR);
            $query -> bindParam(':orderId', $orderId, PDO::PARAM_STR);
            $query -> execute();

            
            
        }

        echo "Order status updated successfully";
    }
    

?>
