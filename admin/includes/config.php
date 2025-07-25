<?php

session_start(); // Start the session
$host = "localhost";
$user = "root";
$password= "";
$db = "piggeryfarm";

try {
	$dbh = new PDO("mysql:dbname=$db;port=3306;host=$host",
		$user,$password);
	//set the PDO error mode to exception
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//echo "Connected Successfully!";
} catch(PDOException $e) {
    // Connection to the database failed. Attempt to connect again for logging.
    // Be careful here. If there's an issue with connecting, you might end up in an infinite loop.
    // Hence, we'll use a different try-catch just for logging.

    try {
        $logDbh = new PDO("mysql:dbname=$db;port=3306;host=$host", $user, $password);

        $stmt = $logDbh->prepare("INSERT INTO error_logs (error_message, stack_trace) VALUES (:error_message, :stack_trace)");
        $stmt->bindParam(':error_message', $e->getMessage());
        $stmt->bindParam(':stack_trace', $e->getTraceAsString());
        $stmt->execute();
    } catch(PDOException $logError) {
        // Logging failed. At this point, you might want to send an email, write to a file, or handle this differently.
        echo "Connection Failed!"; // Avoid giving too much information.
    }
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>