<?php
include('includes/config.php');

if (isset($_POST['pigletsid'])) {
    $pigsId = $_POST['pigletsid'];  

    $sqlDeletepigs = "DELETE FROM piglets WHERE id = :pigsId";
    $stmtDeletepigs = $dbh->prepare($sqlDeletepigs);
    $stmtDeletepigs->bindParam(':pigsId', $pigsId, PDO::PARAM_INT);
    $stmtDeletepigs->execute();

    $sqlqrDeletepigs = "DELETE FROM piglets_qr WHERE piglet_id = :pigsId";
    $stmtqrDeletepigs = $dbh->prepare($sqlqrDeletepigs);
    $stmtqrDeletepigs->bindParam(':pigsId', $pigsId, PDO::PARAM_INT);
    $stmtqrDeletepigs->execute();

}
?>
