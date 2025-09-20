<?php
error_reporting(1);
include('config.php');
    if(isset($_POST['record'])){
        $id = $_POST['id'];
        $by = $_POST['by'];
        $date = $_POST['date'];
        $vaccine = $_POST['vaccine'];
        
        $query = $dbh->prepare("INSERT INTO vaccines_shot (piglets_id,vaccined_by, vaccine_name,date_vaccinated) VALUES (:piglets_id,:vaccined_by,  :vaccine_name, :date_vaccinated)");
    
        // Bind the parameters
        $query->bindParam(':piglets_id', $id, PDO::PARAM_STR);
        $query->bindParam(':vaccined_by', $by, PDO::PARAM_STR);
        $query->bindParam(':vaccine_name', $vaccine, PDO::PARAM_STR);
        $query->bindParam(':date_vaccinated', $date, PDO::PARAM_STR);
    
        try {
            $query->execute();
            if ($query) {
                echo "<script type='text/javascript'>alert('Record Added'); window.location.href = '/piggery/qr_piglets.php?id=" . $id . "';</script>";
            } else {
                $err = "Please Try Again Or Try Later";
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

?>
