<?php
include('includes/config.php');

try {
   

    if (isset($_POST['pigletid'])) {
        $pigletid = intval($_POST['pigletid']); 
    
        try {
            $sqlDelete = "DELETE FROM tblpiglet_for_sale_details WHERE piglet_id = :pigletid";
            $stmtDelete = $dbh->prepare($sqlDelete);
            $stmtDelete->bindParam(':pigletid', $pigletid, PDO::PARAM_INT);
            $stmtDelete->execute();
        
            $sqlUpdate = "UPDATE piglets SET posted = 0 WHERE id = :pigletid";
            $stmtUpdate = $dbh->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':pigletid', $pigletid, PDO::PARAM_INT);
            $stmtUpdate->execute();
        
            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database operation failed.']);
            exit;
        }
    }
    

    if (isset($_POST['cull_id'])) {
        $cull_id = $_POST['cull_id'];

        $sqlDeletecull = "DELETE FROM tblculling WHERE id = :feedId";
        $sqlDeletecull = $dbh->prepare($sqlDeletecull);

        $sqlDeletecull->bindParam(':feedId', $cull_id, PDO::PARAM_INT);

        $sqlDeletecull->execute();
    }

    if (isset($_POST['feedid'])) {
        $feedId = $_POST['feedid'];

        $sqlDeletefeed = "DELETE FROM tblfeeds WHERE id = :feedId";
        $stmtDeletefeed = $dbh->prepare($sqlDeletefeed);

        $stmtDeletefeed->bindParam(':feedId', $feedId, PDO::PARAM_INT);

        $stmtDeletefeed->execute();
    }


    if (isset($_POST['recordid'])) {
        $recordId = $_POST['recordid']; 
        $sqlDeleterecord = "DELETE FROM breeder_records WHERE id = :recordId";
        $stmtDeleterecord = $dbh->prepare($sqlDeleterecord);
        $stmtDeleterecord->bindParam(':recordId', $recordId, PDO::PARAM_INT); 
        $stmtDeleterecord->execute();
    }
    
    if (isset($_POST['vaccinerecordid'])) {
        $recordId = $_POST['recordid']; 
        $sqlDeleterecord = "DELETE FROM vaccines_shot WHERE id = :recordId";
        $stmtDeleterecord = $dbh->prepare($sqlDeleterecord);
        $stmtDeleterecord->bindParam(':recordId', $recordId, PDO::PARAM_INT); 
        $stmtDeleterecord->execute();
    }
    
    if (isset($_POST['vaccinerecordid'])) {
        $recordId = $_POST['vaccinerecordid']; 
    
        $sqlDeleterecord = "DELETE FROM vaccines_shot WHERE id = :recordId";
        $stmtDeleterecord = $dbh->prepare($sqlDeleterecord);
    
        $stmtDeleterecord->bindParam(':recordId', $recordId, PDO::PARAM_INT);
    
        if ($stmtDeleterecord->execute()) {
            $breederId = $_POST['piglets_id'];
            header("Location: unhealthypiglets.php?id=" . $breederId);
            exit;
        } else {
            echo "Error deleting record.";
        }
    } else {
        echo "Record ID not provided.";
    }

    if (isset($_POST['recordid'])) {
        $recordId = $_POST['recordid']; 
    
        $sqlDeleterecord = "DELETE FROM breeder_records WHERE id = :recordId";
        $stmtDeleterecord = $dbh->prepare($sqlDeleterecord);
    
        $stmtDeleterecord->bindParam(':recordId', $recordId, PDO::PARAM_INT);
    
        if ($stmtDeleterecord->execute()) {
            $breederId = $_POST['breeder_id'];
            header("Location: breederdetails.php?id=" . $breederId);
            exit;
        } else {
            echo "Error deleting record.";
        }
    } else {
        echo "Record ID not provided.";
    }

    if (isset($_POST['custid'])) {
        $pigId = $_POST['custid'];

        $sqlDeletepig = "DELETE FROM tblusers WHERE id = :pigId";
        $stmtDeletepig = $dbh->prepare($sqlDeletepig);

        $stmtDeletepig->bindParam(':pigId', $pigId, PDO::PARAM_INT);

        $stmtDeletepig->execute();
    }


    if (isset($_POST['id'])) {
        $pigId = $_POST['id'];

        $sqlDeletepig = "DELETE FROM tblpigforsale WHERE id = :pigId";
        $stmtDeletepig = $dbh->prepare($sqlDeletepig);

        $stmtDeletepig->bindParam(':pigId', $pigId, PDO::PARAM_INT);

        $stmtDeletepig->execute();
    }


    
   

    if (isset($_POST['cullingid'])) {
        $sowId = $_POST['cullingid'];
    
        $dbh->beginTransaction();
    
        try {
            $sqlFetch = "SELECT name, age,img FROM tblpigbreeders WHERE id = :sowId";
            $stmtFetch = $dbh->prepare($sqlFetch);
            $stmtFetch->bindParam(':sowId', $sowId, PDO::PARAM_INT);
            $stmtFetch->execute();
    
            $result = $stmtFetch->fetch(PDO::FETCH_ASSOC);
    
            if ($result) {
                $name = $result['name'];
                $age = $result['age'];
                $img = $result['img'];
    
                $status = "Culling";
                $sqlInsert = "INSERT INTO tblculling (name, age, status,img) VALUES (:name, :age, :status,:img)";
                $stmtInsert = $dbh->prepare($sqlInsert);
                $stmtInsert->bindParam(':name', $name);
                $stmtInsert->bindParam(':age', $age);
                $stmtInsert->bindParam(':status', $status);
                $stmtInsert->bindParam(':img', $img);
                $stmtInsert->execute();
    
                $sqlDelete = "DELETE FROM tblpigbreeders WHERE id = :sowId";
                $stmtDelete = $dbh->prepare($sqlDelete);
                $stmtDelete->bindParam(':sowId', $sowId, PDO::PARAM_INT);
                $stmtDelete->execute();

                
                
                $dbh->commit();
    
            } else {
                throw new Exception("Data not found for provided sowId");
            }
    
        } catch (Exception $e) {
            $dbh->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['pigsid'])) {
        $pigsId = $_POST['pigsid']; 
    
        $sqlDeletepigs = "DELETE FROM tblgrowingphase WHERE id = :pigsId";
        $stmtDeletepigs = $dbh->prepare($sqlDeletepigs);
        $stmtDeletepigs->bindParam(':pigsId', $pigsId, PDO::PARAM_INT);
        $stmtDeletepigs->execute();
    
    }


   
    if (isset($_POST['sowid'])) {
        $sowId = $_POST['sowid']; 
    
        $sqlDeletesow = "DELETE FROM tblpigbreeders WHERE id = :sowId";
        $stmtDeletesow = $dbh->prepare($sqlDeletesow);
    
        $stmtDeletesow->bindParam(':sowId', $sowId, PDO::PARAM_INT);
    
        $stmtDeletesow->execute();
    
    }

    if (isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];

   
        $sql = "SELECT orderstatus FROM tblorders WHERE id = :orderId";
        $query = $dbh->prepare($sql);
        $query->bindParam(':orderId', $orderId, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_ASSOC); 
        
        if ($results['orderstatus'] == 'Pending') {
            $stmt = $dbh->prepare('UPDATE tblpigforsale SET status = NULL WHERE id IN (SELECT pig_id FROM tblorderdetails WHERE order_id = :order_id)');
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
        } else {
            $stmt = $dbh->prepare('DELETE FROM tblpigforsale WHERE id IN (SELECT pig_id FROM tblorderdetails WHERE order_id = :order_id)');
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
        }
        
    }
    
        $sqlDeleteorder = "UPDATE tblorders SET deleted = 1 WHERE id = :orderId";
        $stmtDeleteorder = $dbh->prepare($sqlDeleteorder);

        $stmtDeleteorder->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        $stmtDeleteorder->execute();

        $sqlDeleteorderdetails = "DELETE FROM tblorderdetails WHERE order_id = :orderId";
        $stmtDeleteorderdetails = $dbh->prepare($sqlDeleteorderdetails);

        $stmtDeleteorderdetails->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        $stmtDeleteorderdetails->execute();



    echo "Success";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
