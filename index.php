<?php
require_once("admin/inc/config.php");

// Check elections status (Active/Expired)
$fetchingElections = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
$currentDate = date("Y-m-d");

while ($data = mysqli_fetch_assoc($fetchingElections)) {
    $electionId = $data['id'];
    $status = $data['status'];
    $startingDate = $data['starting_date'];
    $endingDate = $data['ending_date'];

    if ($status === 'Active' && strtotime($endingDate) < strtotime($currentDate)) {
        mysqli_query($db, "UPDATE elections SET status='Expired' WHERE id='$electionId'") or die(mysqli_error($db));
    } elseif ($status === 'InActive' && strtotime($startingDate) <= strtotime($currentDate)) {
        mysqli_query($db, "UPDATE elections SET status='Active' WHERE id='$electionId'") or die(mysqli_error($db));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Voting System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css"/>
</head>
<body>
    <?php if (isset($_GET['signup'])): ?>
    <section class="signup-block">
        <form method="POST">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 signup-sec">
                        <h2 class="text-center">Signup Now</h2>
                        <div class="signup-form">
                            <div class="form-group">
                                <input type="text" name="signup_username" class="form-control" placeholder="Username" required/>
                            </div>
                            <div class="form-group">
                                <input type="text" name="signup_contact" class="form-control" placeholder="Contact Number" required/>
                            </div>
                            <div class="form-group">
                                <input type="password" name="signup_password" class="form-control" placeholder="Password" required/>
                            </div>
                            <div class="form-group">
                                <input type="password" name="signup_retype_password" class="form-control" placeholder="Re-type password" required/>
                            </div>
                            <button type="submit" name="signup_button" class="btn btn-signup float-right">Sign Up</button>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="signup-btn">Already have an account? <a href="?index.php">Sign In</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <img class="d-block img-fluid" src="assets/images/voter.jpg" alt="Voter Image">
                    </div>
                </div>
            </div>
        </form>
    </section>
    <?php else: ?>
    <section class="login-block">
        <div class="container">
            <div class="row">
                <div class="col-md-4 login-sec">
                    <h2 class="text-center">Login Now</h2>
                    <form method="POST" class="login-form">
                        <div class="form-group">
                            <input type="text" name="contact_no" class="form-control" placeholder="Contact No" required/>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Password" required/>
                        </div>
                        <button type="submit" name="loginBtn" class="btn btn-login float-right">Submit</button>
                    </form>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="signup">Don't have an account? <a href="?signup=1">Sign Up</a></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <img class="d-block img-fluid" src="assets/images/voter.jpg" alt="Voter Image">
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>

<?php
if (isset($_POST['signup_button'])) {
    $signupUsername = mysqli_real_escape_string($db, $_POST['signup_username']);
    $signupContact = mysqli_real_escape_string($db, $_POST['signup_contact']);
    $signupPassword = mysqli_real_escape_string($db, $_POST['signup_password']);
    $signupRetypePassword = mysqli_real_escape_string($db, $_POST['signup_retype_password']);
    
    if ($signupPassword === $signupRetypePassword) {
        $hashedPassword = password_hash($signupPassword, PASSWORD_DEFAULT);
        mysqli_query($db, "INSERT INTO users (username, contact_no, password, user_role) VALUES ('$signupUsername', '$signupContact', '$hashedPassword', 'user_role_placeholder')") or die(mysqli_error($db));
        echo "<script> location.assign('index.php?signup=1&registered=1');</script>";
    } else {
        echo "<script> location.assign('index.php?signup=1&invalid=1');</script>";
    }
}elseif (isset($_POST["loginBtn"])) {
    $contactNo = mysqli_real_escape_string($db, $_POST['contact_no']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    
    $fetchingData = mysqli_query($db, "SELECT * FROM users WHERE contact_no = '$contactNo'") or die(mysqli_error($db));
    
    if (mysqli_num_rows($fetchingData) > 0) {
        $data = mysqli_fetch_assoc($fetchingData);
        if (password_verify($password, $data['password'])) {
            session_start();
            $_SESSION['user_role'] = $data['user_role'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['user_id'] = $data['id'];
            
            if ($data['user_role'] === "admin") {
                $_SESSION['key'] = "AdminKey";
                echo "<script>location.assign('admin/index.php?homepage=1');</script>";
            } else {
                $_SESSION['key'] = "VotersKey";
                echo "<script>location.assign('voters/index.php');</script>";
            }
        } else {
            echo "<script> location.assign('index.php?invalid_access=1');</script>";
        }
    } else {
        echo "<script> location.assign('index.php?not_registered=1');</script>";
    }
}
?>
