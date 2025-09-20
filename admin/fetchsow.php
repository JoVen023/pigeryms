<?php



function getPiglet($dbh,$group_id){

    $query = "SELECT `id`, `name`, `gender`, `breed`, COUNT(id) AS total 
              FROM `piglets`  
              WHERE growinphase_id = :id AND posted = 0 
              GROUP BY `name`, `gender`, `breed`, `id`
              ORDER BY `name` ASC";


    $stmt = $dbh->prepare($query);
    try {
        $stmt->execute([':id'=>$group_id]);
    } catch (PDOException $ex) {
        echo $ex->getTraceAsString();
        echo $ex->getMessage();
        exit;
    }
    
    $data = '<option value="">Select Piglet</option>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
       
            $data =
                $data .
                '<option value="' .
                $row['id'] .'" data-pigs="' . $row['name'] . '">' .
                $row['name'] .'(' . $row['total'] . 
                ')</option>';
        }
    return $data;

    }


function getMenutype($dbh, $sowId = 0)
{
    $query = "SELECT 
    tgp.`id`, 
    tgp.`sowname`,
    tgp.`pigs`,
    COUNT(p.id) AS total
FROM `tblgrowingphase` tgp
LEFT JOIN piglets p ON tgp.id = p.growinphase_id
WHERE (tgp.status = 'Grower' OR tgp.status = 'Finisher')
  AND p.move = 0
GROUP BY tgp.`id`, tgp.`sowname`, tgp.`pigs`
ORDER BY tgp.`sowname` ASC
";

    $stmt = $dbh->prepare($query);
    try {
        $stmt->execute();
    } catch (PDOException $ex) {
        echo $ex->getTraceAsString();
        echo $ex->getMessage();
        exit;
    }
    

    $sowCountArray = array();
$countQuery = "SELECT sow_id, COUNT(*) as count FROM tblpigforsale GROUP BY sow_id";
$countStmt = $dbh->prepare($countQuery);
$countStmt->execute();

while ($countRow = $countStmt->fetch(PDO::FETCH_ASSOC)) {
    $sowCountArray[$countRow['sow_id']] = $countRow['count'];
}

    $data = '<option value="">Select Pigs Group</option>';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($sowCountArray[$row['id']]) && $sowCountArray[$row['id']] >= $row['pigs']) {
            continue;  // skip to the next iteration if the count meets or exceeds the pigs value
        }
        if ($sowId == $row['id']) {
            $data =
                $data .
                '<option selected="selected" value="' .
                $row['id'] .'" data-pigs="' . $row['pigs'] . '">'.
                $row['sowname'] . ' (' . $row['total'] . 
                ')</option>';
        } else {
            $data =
                $data .
                '<option value="' .
                $row['id'] .'" data-pigs="' . $row['pigs'] . '">' .
                $row['sowname'] .'(' . $row['total'] . 
                ')</option>';
        }
    }
    return $data;
}



function getPigletgroup($dbh, $pigletId = 0)
{
    $query = "SELECT `id`, `sowname`,`pigs`FROM `tblgrowingphase`  WHERE status = 'Piggybloom' ORDER BY `sowname` ASC";

    $stmt = $dbh->prepare($query);
    try {
        $stmt->execute();
    } catch (PDOException $ex) {
        echo $ex->getTraceAsString();
        echo $ex->getMessage();
        exit;
    }
    

    $sowCountArray = array();
$countQuery = "SELECT sow_id, COUNT(*) as count FROM tblpigforsale GROUP BY sow_id";
$countStmt = $dbh->prepare($countQuery);
$countStmt->execute();

while ($countRow = $countStmt->fetch(PDO::FETCH_ASSOC)) {
    $sowCountArray[$countRow['sow_id']] = $countRow['count'];
}

    $data = '<option value="">Select Pigs Group</option>';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($sowCountArray[$row['id']]) && $sowCountArray[$row['id']] >= $row['pigs']) {
            continue;  // skip to the next iteration if the count meets or exceeds the pigs value
        }
        if ($pigletId == $row['id']) {
            $data =
                $data .
                '<option selected="selected" value="' .
                $row['id'] .'" data-pigs="' . $row['pigs'] . '">'.
                $row['sowname'] . '</option>';
        } else {
            $data =
                $data .
                '<option value="' .
                $row['id'] .'" data-pigs="' . $row['pigs'] . '">' .
                $row['sowname'] .'</option>';
        }
    }
    return $data;
}




function getsowparent($dbh, $sowId = 0)
{
    $query = "SELECT `id`, `name` FROM `tblpigbreeders`  ORDER BY `name` ASC;";

    $stmt = $dbh->prepare($query);
    try {
        $stmt->execute();
    } catch (PDOException $ex) {
        echo $ex->getTraceAsString();
        echo $ex->getMessage();
        exit;
    }

    $data = '<option value="">Select Sow</option>';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($sowId == $row['id']) {
            $data =
                $data .
                '<option selected="selected" value="' .
                $row['id'] .
                '">' .
                $row['name'] .
                '</option>';
        } else {
            $data =
                $data .
                '<option value="' .
                $row['id'] .
                '">' .
                $row['name'] .
                '</option>';
        }
    }
    return $data;
}


?>