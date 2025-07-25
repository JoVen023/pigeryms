<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['dark_mode'] = isset($_POST['dark-mode']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>