<?php
include('includes/config.php');

if (isset($_POST['healthy_id'])) {
    $pigletId = $_POST['healthy_id'];

    $sqlFetch = "SELECT piglet_id FROM unhealthy_piglets WHERE id = :pigletId";
    $stmtFetch = $dbh->prepare($sqlFetch);
    $stmtFetch->bindParam(':pigletId', $pigletId, PDO::PARAM_INT);
    $stmtFetch->execute();

    $result = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $piglet_id = $result['piglet_id'];

        $status = "Healthy";
        $sqlUpdatePiglets = "UPDATE piglets SET status = :status WHERE id = :piglet_id";
        $stmtUpdate = $dbh->prepare($sqlUpdatePiglets);
        $stmtUpdate->bindParam(':piglet_id', $piglet_id, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':status', $status, PDO::PARAM_STR);
        $stmtUpdate->execute();

        $statusRecovered = "Recovered";
        $sqlUpdateUnhealthy = "UPDATE unhealthy_piglets SET status = :status WHERE id = :id";
        $stmtUpdateUnhealthy = $dbh->prepare($sqlUpdateUnhealthy);
        $stmtUpdateUnhealthy->bindParam(':status', $statusRecovered, PDO::PARAM_STR);
        $stmtUpdateUnhealthy->bindParam(':id', $pigletId, PDO::PARAM_INT);
        $stmtUpdateUnhealthy->execute();

        echo json_encode(['success' => true, 'message' => 'Piglet status updated']);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Piglet not found']);
        exit;
    }
}
?>
