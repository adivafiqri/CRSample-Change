<?php
include 'functions.php';
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['token'])) {
    header("location: login.php");
} else {
    $pdo = pdo_connect();
    $stmt = $pdo->prepare('SELECT * FROM contacts');
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);




    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        header("location:index.php");
    } else {
        die('No ID specified!');
    }
}
