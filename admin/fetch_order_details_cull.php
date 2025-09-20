<?php

error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors in output

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit; // It's important to stop further script execution
} 

if ($_POST['orderId']) {
    $orderId = $_POST['orderId'];

    $sql = "SELECT * FROM tblorderdetails WHERE order_id = :orderId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':orderId', $orderId, PDO::PARAM_STR);
    try {
        $query->execute();
    } catch(PDOException $e) {
        echo "Query failed: " . $e->getMessage();
        exit;
    }
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        // Initialize the variable to store the HTML
        

        $html = ' <div class="table-responsive">';
        $html .= ' <table class="table caption-top">';
        $html .= ' <caption>List of Pigs</caption>';
        $html .= '<thead>';
        $html .= '<tr><th scope="col">Id</th><th scope="col">Name</th><th scope="col">Sex</th><th scope="col" >Age</th><th scope="col">Price</th><th scope="col">Quantity</th></tr>';
        $html .= '</thead>';
        $html .='<tbody>';
        foreach ($results as $result) {
          
            $html .= '<tr>';
            $html .= '<td>' . htmlentities($result->id) . '</td>';
            $html .= '<td>' . htmlentities($result->name) . '</td>';
            $html .= '<td>' . htmlentities($result->sex ?? 'Female') . '</td>';

            $html .= '<td >' . htmlentities($result->age) . '</td>';
            $html .= '<td>';
            $html .= '<input type="number" style="width: 80px;" class="form-control orderPrice" data-detail-id="' . htmlentities($result->id) . '" value="' . htmlentities($result->price) . '" required>';
            $html .= '</td>';
            $html .= '<td >' . htmlentities($result->quantity) . '</td>';
        
            $html .= '<td>';
            // $html .= '<input type="number" style="width: 100px;" class="form-control orderWeight" data-detail-id="' . htmlentities($result->id) . '" data-sow-id="' . htmlentities($result->sow_id) . '" data-price="' . htmlentities($result->price) . '" placeholder="Weight" required>';
            $html .= '</td>';
            $html .= '</tr>';
            
        }

        $html .='</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        echo $html;
    } else {
        echo "No order details found for order ID $orderId";
    }
} else {
    echo "No order ID provided";
}

?>
