<?php
include('includes/config.php');
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

$response = null;

if (isset($_POST['feedId'])) {
    $feedId = $_POST['feedId'];
    $sql = "SELECT * FROM tblfeeds WHERE id = :feedId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':feedId', $feedId, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $response = [
            "id" => $result->id,
            "name" => $result->feedsname,
            "quantity" => $result->quantity,
            "price" => $result->price,
            "date" => $result->datepurchased,
            "consumedate" => $result->consumedate
        ];
    } else {
        $response = ["error" => "No feed found with id $feedId"];
    }
}
elseif (isset($_GET['piglet_id'])) {
    $piglet_id = intval($_GET['piglet_id']);
    $query = "SELECT id, gender FROM piglets WHERE id = :id";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':id', $piglet_id, PDO::PARAM_INT);
    $stmt->execute();
    $piglet = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($piglet) {
        $response = $piglet;
    } else {
        $response = ["error" => "No piglet found with this ID: $piglet_id"];
    }
}
elseif (isset($_POST['feedIds'])) {
    $feedId = $_POST['feedIds'];
    $sql = "SELECT * FROM breeder_records WHERE id = :feedId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':feedId', $feedId, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $response = [
            "id" => $result->id,
            "date_farrowed" => $result->date_farrowed,
            "weaned_date" => $result->weaned_date,
            "total_piglets" => $result->total_piglets,
            "survived" => $result->survived
        ];
    } else {
        $response = ["error" => "No record found with id $feedId"];
    }
}
else {
    $response = ["error" => "No valid parameter provided"];
}

echo json_encode($response);
exit;
