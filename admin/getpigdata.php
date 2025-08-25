<?php
include('includes/config.php');
header('Content-Type: application/json'); 

if (strlen($_SESSION['alogin']) == 0) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
} 

$response = [];

if (!empty($_POST['pigId'])) {
    $pigId = $_POST['pigId'];

    $sql = "SELECT * FROM tblpigforsale WHERE id = :pigId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pigId', $pigId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $response = [
            "id" => $result->id,
            "name" => $result->name,
            "sex" => $result->sex,
            "age" => $result->age,
            "weight_class" => $result->weight_class,
            "price" => $result->price,
            "img" => $result->img,
            "back" => $result->back,
            "side" => $result->side,
            "front" => $result->front
        ];
    } else {
        $response = ["error" => "No pig found with id $pigId"];
    }
}

if (!empty($_POST['forpigletsId'])) {
    $pigletsId = $_POST['forpigletsId'];

    $sql = "SELECT tfsd.*, tfs.Farrowed_Date, tfs.name ,tfs.id as main_id
            FROM tblpiglet_for_sale_details tfsd
            LEFT JOIN tblpiglet_for_sale tfs 
                ON tfsd.tblpiglet_for_sale_id = tfs.id
            WHERE tfsd.id = :pigletId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pigletId', $pigletsId, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $response = [
            "id" => $result->id,
            "main_id" => $result->main_id,
            "name" => $result->name,
            "gender" => $result->gender,
            "piglet_weight" => $result->piglet_weight,
            "farrowed" => $result->Farrowed_Date,
            "price" => $result->price,
            "img" => $result->img
        ];
    } else {
        $response = ["error" => "No piglet found with id $pigletsId"];
    }
}

echo json_encode($response);
exit;
