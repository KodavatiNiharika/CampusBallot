<?php
    session_start();
    require_once("config.php");

    if($_SESSION['key'] != "AdminKey")
    {
        echo "<script> location.assign('logout.php');</script>";
        die;
    }


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdminPanel - Online Voting System</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row text-white" style="background-color: teal;">
            <div class="col-1">
                <img src="../assets/images/votyah.gif" width="100px"/>
            </div>
            <div class="col-11 my-auto">
                <h3> ONLINE VOTING SYSTEM - <small> Welcome  <?php echo $_SESSION['username'];?></small></h3>
            </div>

    </div>
</div>