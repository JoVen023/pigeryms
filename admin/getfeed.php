<?php
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit; // It's important to stop further script execution
} 

header('Content-Type: application/json');

if (isset($_POST['feedId'])) {
    $feedId = $_POST['feedId'];
    $sql = "SELECT * FROM tblfeeds WHERE id = :feedId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':feedId', $feedId, PDO::PARAM_STR);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        // Create an associative array with the feed data
        $feedData = array(
            "id" => $result->id,
            "name" => $result->feedsname,
            "quantity" => $result->quantity,
            "price" => $result->price,
            "date" => $result->datepurchased,
            "consumedate" => $result->consumedate
        );
        echo json_encode($feedData);
    } else {
        echo json_encode(array("error" => "No feed found with id $feedId"));
    }
} else {
    echo json_encode(array("error" => "No feedId provided"));
}


// In getfeed.php
if (isset($_POST['feedIds'])) {
    $feedId = $_POST['feedIds'];  // Correct variable name
    $sql = "SELECT * FROM breeder_records WHERE id = :feedId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':feedId', $feedId, PDO::PARAM_STR);  // Correct parameter name
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
        echo json_encode(array("error" => "No record found with id $feedId"));
    }
} else {
    echo json_encode(array("error" => "No feedId provided"));
}


?>
