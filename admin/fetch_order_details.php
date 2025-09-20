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
        echo "Error: " . $e->getMessage();
        exit;
    }
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        $first = $results[0];
        $piglets=($first->piglet == 1 )?'':'<th scope="col" >Weight Class</th>';
        $price=($first->piglet == 1 )?'Price':'Price/kg';
        $html = ' <div class="table-responsive">';
        $html .= ' <table class="table caption-top">';
        $html .= ' <caption>List of Pigs</caption>';
        $html .= '<thead>';
        $html .= '<tr><th scope="col">Id</th><th scope="col">Name</th><th scope="col">Sex</th><th scope="col" >Age</th>'.$piglets.'<th scope="col">'. $price.'</th><th scope="col">Quantity</th><th scope="col">Weight</th></tr>';
        $html .= '</thead>';
        $html .='<tbody>';
        foreach ($results as $result) {

            $piglet=$result->piglet;

            $weightClass = $result->weight_class;
            preg_match_all('/\d+/', $weightClass, $matches);
            $minWeight = isset($matches[0][0]) ? (int) $matches[0][0] : 0;
            $maxWeight = isset($matches[0][1]) ? (int) $matches[0][1] : 0;

          
            $html .= '<tr>'; 
            $html .= '<td>' . htmlentities($result->id) . '</td>';
            $html .= '<td>' . htmlentities($result->name) . '</td>';
            $html .= '<td>' . htmlentities($result->sex ?? 'Female') . '</td>';

            $html .= '<td >' . htmlentities($result->age) . '</td>';
            if($piglet == 0){
                $html .= '<td >' . htmlentities($result->weight_class) . '</td>';
            }
            $html .= '<td>';
            $html .= '<input type="number" style="width: 80px;" class="form-control orderPrice" data-detail-id="' . htmlentities($result->id) . '" value="' . htmlentities($result->price) . '" required>';
            $html .= '</td>';
            $html .= '<td >' . htmlentities($result->quantity) . '</td>';
        
            $html .= '<td>';
            $html .= '<input type="number" style="width: 100px;" class="form-control orderWeight" 
            data-detail-id="' . htmlentities($result->id) . '" 
            data-sow-id="' . htmlentities($result->sow_id) . '" 
            data-price="' . htmlentities($result->price) . '" 
             data-min="' . htmlentities($minWeight) . '" 
              data-max ="' . htmlentities($maxWeight) . '" 
           value="' . htmlentities($result->weight_class) . '"
            placeholder="Weight" required>';
            $html .= '<div class="text-danger weight-error" style="display:none; font-size: 0.9em;"></div>';
            
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
    // No orderId provided, return an error message
    echo "No order ID provided";
}

?>
