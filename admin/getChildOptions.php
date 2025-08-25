<?php

include('includes/config.php');
if (isset($_GET['sowid'])) {
    
    $parentId = intval($_GET['sowid']);

 error_log("Parent ID: " . $parentId);

    $response = [];

    // Database query to fetch child options
    try {
        // Sample database query
        $query = "SELECT id, name FROM piglets WHERE growinphase_id = :sow_id AND piglets.move= 0 ";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':sow_id', $parentId, PDO::PARAM_INT);
        $stmt->execute();
    
        // Fetch the child pigs
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($children) {
            // If children are found, return them as the response
            $response = $children;
        } else {
            // If no children are found, return an error message
            $response = ['error' => 'No piglets found for the selected sow.'];
        }
    
    } catch (PDOException $ex) {
        // Log the detailed database error message
        error_log('Database error: ' . $ex->getMessage());
        
        // Return a detailed error message for the frontend
        $response = ['error' => 'Database query failed: ' . $ex->getMessage()];
    }
    
    echo json_encode($response); 
}

if (isset($_GET['pigletid'])) {
    
    $parentId = intval($_GET['pigletid']);

 error_log("Parent ID: " . $parentId);

    $response = [];

    // Database query to fetch child options
    try {
        // Sample database query
        $query = "SELECT id, sowname,weaneddate FROM tblgrowingphase WHERE id = :id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $parentId, PDO::PARAM_INT);
        $stmt->execute();
    
        // Fetch the child pigs
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($children) {
            // If children are found, return them as the response
            $response = $children;
        } else {
            // If no children are found, return an error message
            $response = ['error' => 'No piglets found for the selected sow.'];
        }
    
    } catch (PDOException $ex) {
        // Log the detailed database error message
        error_log('Database error: ' . $ex->getMessage());
        
        // Return a detailed error message for the frontend
        $response = ['error' => 'Database query failed: ' . $ex->getMessage()];
    }
    
    echo json_encode($response); 
}

?>