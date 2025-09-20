<?php

error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors in output

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit; // It's important to stop further script execution
} 

if (isset($_POST['sowId'])) {
    $sowId = $_POST['sowId'];

    $sql = "SELECT * FROM tblculling WHERE id = :sowId";
    $querys = $dbh->prepare($sql);
    $querys->bindParam(':sowId', $sowId, PDO::PARAM_STR);
   
    $querys->execute();
    $results = $querys->fetchAll(PDO::FETCH_OBJ);

    if ($querys->rowCount() > 0) {
        // Initialize the variable to store the HTML
        

        $html = ' <div class="table-responsive">';
        $html .= ' <table class="table caption-top">';
        $html .= '<thead>';
        $html .= '<tr><th scope="col">Id</th><th scope="col">Name</th><th scope="col" >Age</th><th scope="col" >Status</th>';
        $html .= '</thead>';
        $html .='<tbody>';
        foreach ($results as $result) {
          
            $html .= '<tr>';
            $html .= '<td>' . htmlentities($result->id) . '</td>';
            $html .= '<td>' . htmlentities($result->name) . '</td>';
            $html .= '<td >' . htmlentities($result->age) . '</td>';
            $html .= '<td >' . htmlentities($result->status) . '</td>';
            // Insert a weight input field for the user to enter the weight
            $html .= '</tr>';
            
        }

        // Close the table
        $html .='</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        // Return the HTML
        echo $html;
    } else {
        // No order details found, return a message
        echo "No cull details found for cull ID $sowId";
    }
}


if (isset($_POST['cullorderId'])) {
    $orderId = $_POST['cullorderId'];

    $sql = "SELECT * FROM tblorderdetails WHERE order_id = :orderId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':orderId', $orderId, PDO::PARAM_STR);
  
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        // Initialize the variable to store the HTML
        

        $html = ' <div class="table-responsive">';
        $html .= ' <table class="table caption-top">';
        $html .= ' <caption>List of Pigs</caption>';
        $html .= '<thead>';
        $html .= '<tr><th scope="col">Id</th><th scope="col">Name</th><th scope="col">Sex</th><th scope="col" >Age</th><th scope="col">Price</th><th scope="col">Quantity</th>';
        $html .= '</thead>';
        $html .='<tbody>';
        foreach ($results as $result) {
          $weight =($result->piglet == 1)? htmlentities($result->weight_class): htmlentities($result->weight);
            $html .= '<tr>';
            $html .= '<td>' . htmlentities($result->id) . '</td>';
            $html .= '<td>' . htmlentities($result->name) . '</td>';
            $html .= '<td>' . htmlentities($result->sex) . '</td>';
            $html .= '<td >' . htmlentities($result->age) . '</td>';
            $html .= '<td ><span>&#8369;</span>' . htmlentities($result->price) . '</td>';
            $html .= '<td >' . htmlentities($result->quantity) . '</td>';
         
           
            // Insert a weight input field for the user to enter the weight
          
            $html .= '</tr>';
            
        }

        // Close the table
        $html .='</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        // Return the HTML
        echo $html;
    } else {
        // No order details found, return a message
        echo "No order details found for order ID $orderId";
    }
}


if (isset($_POST['orderId'])) {
    $orderId = $_POST['orderId'];

    $sql = "SELECT * FROM tblorderdetails WHERE order_id = :orderId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':orderId', $orderId, PDO::PARAM_STR);
  
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        // Initialize the variable to store the HTML
        

        $html = ' <div class="table-responsive">';
        $html .= ' <table class="table caption-top">';
        $html .= ' <caption>List of Pigs</caption>';
        $html .= '<thead>';
        $html .= '<tr><th scope="col">Id</th><th scope="col">Name</th><th scope="col">Sex</th><th scope="col" >Age</th><th scope="col">Price</th><th scope="col">Quantity</th><th scope="col">Weight</th>';
        $html .= '</thead>';
        $html .='<tbody>';
        foreach ($results as $result) {
          $weight =($result->piglet == 1)? htmlentities($result->weight_class): htmlentities($result->weight);
            $html .= '<tr>';
            $html .= '<td>' . htmlentities($result->id) . '</td>';
            $html .= '<td>' . htmlentities($result->name) . '</td>';
            $html .= '<td>' . htmlentities($result->sex) . '</td>';
            $html .= '<td >' . htmlentities($result->age) . '</td>';
            $html .= '<td ><span>&#8369;</span>' . htmlentities($result->price) . '</td>';
            $html .= '<td >' . htmlentities($result->quantity) . '</td>';
            $html .= "<td>{$weight} kg</td>";
           
            // Insert a weight input field for the user to enter the weight
          
            $html .= '</tr>';
            
        }

        // Close the table
        $html .='</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        // Return the HTML
        echo $html;
    } else {
        // No order details found, return a message
        echo "No order details found for order ID $orderId";
    }
}

?>
