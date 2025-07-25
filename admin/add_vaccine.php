<?php
error_reporting(1);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
} else {
    if (isset($_GET['id'])) {
        $pigletId = intval($_GET['id']);
    } else {
       
        die('ID not provided.');
    }
    if(isset($_POST['record'])){
        $id = $pigletId;
        $vaccine = $_POST['vaccine'];
        $date = $_POST['date'];
        
        $query = $dbh->prepare("INSERT INTO vaccines_shot (piglets_id, vaccine_name,date_vaccinated) VALUES (:piglets_id,  :vaccine_name, :date_vaccinated)");
    
        // Bind the parameters
        $query->bindParam(':piglets_id', $id, PDO::PARAM_STR);
        $query->bindParam(':vaccine_name', $vaccine, PDO::PARAM_STR);
        $query->bindParam(':date_vaccinated', $date, PDO::PARAM_STR);
    
        try {
            $query->execute();
            if ($query) {
                echo "<script type='text/javascript'>alert('Record Added'); window.location.href = 'unhealthypigletdetails.php?id=" . $id . "';</script>";

                
            } else {
                $err = "Please Try Again Or Try Later";
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

}
?>
