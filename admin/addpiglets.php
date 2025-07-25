<?php
include('includes/config.php');
require_once('includes/phpqrcode/qrlib.php'); 
$host = gethostbyname(gethostname());


if (isset($_POST['add'])) {
    $pigname = $_POST['name'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $growingphase_id = $_POST['id'];
    $filename = null;

    try {
        if ($_FILES['pict']['error'] == UPLOAD_ERR_OK) {
            $filename = basename($_FILES['pict']['name']);
            $uploadPath = 'img/qr_piglets' . $filename;

            if (!move_uploaded_file($_FILES['pict']['tmp_name'], $uploadPath)) {
                $filename = null;
            }
        }

        $query = $dbh->prepare("INSERT INTO piglets(growinphase_id, name, gender, status, img)
                                VALUES(:growinphase_id, :name, :gender, :status, :pict)");

        $query->bindParam(':growinphase_id', $growingphase_id, PDO::PARAM_INT);
        $query->bindParam(':name', $pigname, PDO::PARAM_STR);
        $query->bindParam(':gender', $gender, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':pict', $filename, PDO::PARAM_STR);

        $query->execute();
        $piglet_id = $dbh->lastInsertId();


       qr_piglets($piglet_id,$dbh,$host);


        if ($status == 'UnHealthy') {
            $details = $_POST['details'];
            $date_started = $_POST['date_started'];
            $diagnosedStatus = 'Diagnosed';
            $query1 = $dbh->prepare("INSERT INTO unhealthy_piglets(piglet_id, details, status, date)
                                     VALUES(:piglet_id, :details, :status, :date)");

            $query1->bindParam(':piglet_id', $piglet_id, PDO::PARAM_INT);
            $query1->bindParam(':details', $details, PDO::PARAM_STR);
            $query1->bindParam(':status', $diagnosedStatus, PDO::PARAM_STR);
            $query1->bindParam(':date', $date_started, PDO::PARAM_STR);

            $query1->execute();
        }

        if ($query) {
            if( $status == 'UnHealthy'){
                echo "<script type='text/javascript'>alert('Added Successfully. Visit UnHealthy Piglets to check its details...'); 
                   window.location.href = 'growingphasedetails.php?id=" . $growingphase_id . "';</script>";
            }else{
            echo "<script type='text/javascript'>alert('Added Successfully'); 
                  window.location.href = 'growingphasedetails.php?id=" . $growingphase_id . "';</script>";
            }
        } else {
            $err = "Please Try Again Or Try Later";
        }
        
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        header("Location: growingphasedetails.php?msg=error");
        exit;
    }
}

function qr_piglets($piglet_id,$dbh,$host){
    $qrDir = 'img/qr_piglets/';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0755, true);
    }
    $qrFileName = $qrDir . 'piglet_' . $piglet_id . '.png';
    // $qrContent = "https://yourdomain.com/piglet_profile.php?id=$piglet_id";
    $qrContent = "http://$host/piggery/qr_piglets.php?id=$piglet_id";
    
    QRcode::png($qrContent, $qrFileName, QR_ECLEVEL_L, 4);
    
    $pigletQR = $dbh->prepare("INSERT INTO  piglets_qr(piglet_id,img) VALUES(:id,:img)");
    $pigletQR->bindParam(':id', $piglet_id, PDO::PARAM_STR);
    $pigletQR->bindParam(':img', $qrFileName, PDO::PARAM_STR);
    $pigletQR->execute();    

}

?>
