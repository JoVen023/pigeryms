<?php
include('includes/config.php');

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

$sql = "SELECT * FROM tblsoworder WHERE date BETWEEN :startDate AND :endDate";

$query = $dbh->prepare($sql);
$query->bindParam(':startDate', $startDate, PDO::PARAM_STR);
$query->bindParam(':endDate', $endDate, PDO::PARAM_STR);
$query->execute();

$results = $query->fetchAll(PDO::FETCH_NUM);

$response = [
    "data" => $results
];

header('Content-Type: application/json');
echo json_encode($response);
?>