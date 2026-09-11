<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: /Project/login.php");

    exit;
}

?>