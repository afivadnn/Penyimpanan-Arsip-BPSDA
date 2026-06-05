<?php
include_once '../utils/functions.php';
session_destroy();
redirect('login.php');
?>