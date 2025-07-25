<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
include('includes/acc.php');
include('includes/config.php');
include 'fetchsow.php';

function sendEmail($note) {
    global $config;
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'chrisamizola93@gmail.com';
    $mail->Password = 'pjiuquzkspputses';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->isHTML(true);
    $mail->setFrom('chrisamizola93@gmail.com', 'Automated Note Reminder');
    $mail->addAddress('chrisamizola93@gmail.com.com');
    $mail->Subject = "Reminder for: " . $note->name;
    $mail->Body = "Note Details: " . $note->details . "<br>Date: " . $note->time . " 'Today'";
    
    try {
        $mail->send();
    } catch (Exception $e) {
        // Log or print the error if needed
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}

function markAsSent($dbh, $id) {
    $updateStmt = $dbh->prepare("UPDATE tbltodo SET emailed = 1 WHERE id = :id");
    $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $updateStmt->execute();
}

$currentDate = date('Y-m-d');  // Get the current date in the format 'YYYY-MM-DD'
$stmttodo = $dbh->prepare("SELECT tblpigbreeders.id, tblpigbreeders.name, tbltodo.* 
                           FROM tbltodo 
                           INNER JOIN tblpigbreeders ON tbltodo.sow_id = tblpigbreeders.id 
                           WHERE tbltodo.time >= :currentDate1 AND tbltodo.emailed = 0
                           ORDER BY ABS(DATEDIFF(tbltodo.time, :currentDate2)) ASC");
$stmttodo->bindParam(':currentDate1', $currentDate, PDO::PARAM_STR);
$stmttodo->bindParam(':currentDate2', $currentDate, PDO::PARAM_STR);
$stmttodo->execute();

$todo = $stmttodo->fetchAll(PDO::FETCH_OBJ);
foreach ($todo as $to) {
    $date = new DateTime($to->time);
    $dates = $date->format('Y-m-d');
    
    if ($dates == $currentDate && $to->emailed == 0) {
        sendEmail($to);  // Send the email
        markAsSent($dbh, $to->id);  // Mark as sent in the database
    }
}

?>
