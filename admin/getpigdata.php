<?php

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit; // It's important to stop further script execution
} 

if ($_POST['pigId']) {
    $pigId = $_POST['pigId'];

    $sql = "SELECT * FROM tblpigforsale WHERE id = :pigId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pigId', $pigId, PDO::PARAM_STR);
    $query->execute();

    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        // Initialize the variable to store the HTML
        if ($result) {
            // Create an associative array with the pig data
            $pigData = array(
                "id" => $result->id,
                "name" => $result->name,
                "sex" => $result->sex,
                "age" => $result->age,
                "weight_class" => $result->weight_class,
                "price" => $result->price,
                "img" => $result->img,
                "back" => $result->back,
                "side" => $result->side,
                "front" => $result->front,

            );
        
            // Encode the array as a JSON object and output it
            echo json_encode($pigData);
        } else {
            echo json_encode(array("error" => "No pig found with id $pigId"));
        }
    }
}   
    ?>
