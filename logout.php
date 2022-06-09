<?php
session_start();
session_destroy();
unset($_SESSION["token"]);
header('location:login.php');
