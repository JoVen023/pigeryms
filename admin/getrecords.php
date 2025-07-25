<?php
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit; // It's important to stop further script execution
} 

header('Content-Type: application/json');


// In getfeed.php
if (isset($_POST['feedIds'])) {
    $feedIds = $_POST['feedIds'];  // Correct variable name
    $sql = "SELECT * FROM breeder_records WHERE id = :feedId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':feedId', $feedIds, PDO::PARAM_STR);  // Correct parameter name
    $query->execute();

    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        // Create an associative array with the feed data
        $feedData = array(
            "id" => $result->id,
            "date_farrowed" => $result->date_farrowed,
            "weaned_date" => $result->weaned_date,
            "total_piglets" => $result->total_piglets,
            "survived" => $result->survived
        );
        echo json_encode($feedData);  // Correct variable name
    } else {
        echo json_encode(array("error" => "No record found with id $feedIds"));
    }
} else {
    echo json_encode(array("error" => "No feedId provided"));
}


?>
