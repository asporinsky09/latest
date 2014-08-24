<?php

include_once 'bh-config.php';   // Needed because functions.php is not included

$db = new mysqli(DB_HOST,DB_USER, DB_PASSWORD, DB_DATABASE);
if ($db->connect_error) {
    header("Location: ../error.php?err=Unable to connect to MySQL");
    exit();
}