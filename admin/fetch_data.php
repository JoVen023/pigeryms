<?php
include('includes/config.php');

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

$sql = "SELECT  tblusers.id, tblusers.FullName, tblorders.id as order_id,tblorders.orderdate,tblorders.deliverydate, tblorders.orderstatus,tblorders.total_amount,tblorders.mop, tblorders.cust_id  FROM tblusers 
JOIN tblorders ON tblusers.id = tblorders.cust_id 
WHERE tblorders.orderstatus = 'Completed' 
AND tblorders.orderdate BETWEEN :startDate AND :endDate"; 

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