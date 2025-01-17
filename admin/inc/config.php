<?php
    $db = mysqli_connect("localhost","","","onlinevotingsystem",3307) or die("Connectivity Failed");
?>
<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Default password for XAMPP
$dbname = "onlinevotingsystem";

// Create connection
$db = new mysqli("localhost", "", "", "onlinevotingsystem",3307);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
