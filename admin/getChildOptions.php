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





if (isset($_GET['piglet_name'])) {
    $piglet_id = intval($_GET['piglet_name']);

    error_log("Piglet ID: " . $piglet_id);

    try {
        $query = "SELECT p.id, p.name,p.gender,
        TIMESTAMPDIFF(MONTH,tgp.weaneddate,CURDATE()) AS age FROM piglets p 
        LEFT JOIN tblgrowingphase tgp ON p.growinphase_id = tgp.id   
        WHERE p.id = :id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $piglet_id, PDO::PARAM_INT);
        $stmt->execute();

        $piglet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($piglet) {
            echo json_encode($piglet);
        } else {
            echo json_encode(['error' => 'No piglet found with this ID.']);
        }

    } catch (PDOException $ex) {
        error_log('Database error: ' . $ex->getMessage());
        echo json_encode(['error' => 'Database query failed.']);
    }
}



if (isset($_GET['piglet_id'])) {
    $piglet_id = intval($_GET['piglet_id']);

    error_log("Piglet ID: " . $piglet_id);

    try {
        $query = "SELECT id, gender FROM piglets WHERE id = :id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':id', $piglet_id, PDO::PARAM_INT);
        $stmt->execute();

        $piglet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($piglet) {
            echo json_encode($piglet);
        } else {
            echo json_encode(['error' => 'No piglet found with this ID.']);
        }

    } catch (PDOException $ex) {
        error_log('Database error: ' . $ex->getMessage());
        echo json_encode(['error' => 'Database query failed.']);
    }
}




if (isset($_GET['pigletid'])) {
    $piglet_id = intval($_GET['pigletid']);
    try {
        $query1 = "SELECT weaneddate FROM tblgrowingphase WHERE id = :id";
        $stmt1 = $dbh->prepare($query1);
        $stmt1->bindParam(':id', $piglet_id, PDO::PARAM_INT);
        $stmt1->execute();
        $weaned_date = $stmt1->fetch(PDO::FETCH_ASSOC);
        $query = "SELECT id, name FROM piglets WHERE growinphase_id = :id AND posted = 0";
        $stmt = $dbh->prepare($query); 
        $stmt->bindParam(':id', $piglet_id, PDO::PARAM_INT);
        $stmt->execute();
        $piglet = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            'weaned_date' => $weaned_date ? $weaned_date['weaneddate'] : null,
            'piglets' => $piglet
        ];
        echo json_encode($response);

    } catch (PDOException $ex) {
        error_log('Database error: ' . $ex->getMessage());
        echo json_encode(['error' => 'Database query failed.']);
    }
} 

    ?>