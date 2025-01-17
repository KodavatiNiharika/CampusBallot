<?php
include('./config.php');  // Adjust the path if needed
?>
<?php
// Fetch existing candidate details if 'id' is passed


$candidate = null;
if (isset($_GET['id'])) {
    $candidate_id = $_GET['id'];
    // Fetch the candidate details from the database
    $fetchCandidateQuery = mysqli_query($db, "SELECT * FROM candidate_details WHERE id='$candidate_id'");
    $candidate = mysqli_fetch_assoc($fetchCandidateQuery);
    if (!$candidate) {
        echo "<script> location.assign('index.php?candidateNotFound=1');</script>";
        exit();  // Stop the script if the candidate is not found
    }
}

// Handling success or error messages
if (isset($_GET['added'])) {
?>
    <div class="alert alert-success" role="alert">
        Candidate has been added successfully!
    </div>
<?php
} else if (isset($_GET['largeFile'])) {
?>
    <div class="alert alert-danger my-3" role="alert">
        Candidate image is too large, please upload a smaller file (you can upload up to 2MB).
    </div>
<?php
} else if (isset($_GET['invalidFile'])) {
?>
    <div class="alert alert-danger my-3" role="alert">
        Invalid image type (Only .jpg, .png files are allowed).
    </div>
<?php
} else if (isset($_GET['failed'])) {
?>
    <div class="alert alert-danger my-3" role="alert">
        Image uploading failed. Please try again.
    </div>
<?php
} else if (isset($_GET['deleteSuccess'])) {
?>
    <div class="alert alert-success" role="alert">
        Candidate deleted successfully!
    </div>
<?php
} else if (isset($_GET['deleteFailed'])) {
?>
    <div class="alert alert-danger" role="alert">
        Failed to delete candidate. Please try again.
    </div>
<?php
} else if (isset($_GET['editSuccess'])) {
?>
    <div class="alert alert-success" role="alert">
        Candidate details updated successfully!
    </div>
<?php
}
?>

<div class="row mt-3 my-3">
    <div class="col-4">
        <h3><?php echo isset($candidate) ? "Edit Candidate" : "Add New Candidate"; ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <select class="form-control" name="election_id" required>
                    <option value="">Select Election</option>
                    <?php
                    $fetchingElections = mysqli_query($db, "SELECT * FROM elections") OR die(mysqli_error($db));
                    while ($row = mysqli_fetch_assoc($fetchingElections)) {
                        $selected = isset($candidate) && $candidate['election_id'] == $row['id'] ? "selected" : "";
                        echo "<option value='" . $row['id'] . "' $selected>" . $row['election_topic'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="candidate_name" placeholder="Candidate Name" class="form-control" required value="<?php echo isset($candidate) ? htmlspecialchars($candidate['candidate_name']) : ''; ?>"/>
            </div>
            <div class="form-group">
                <input type="file" name="candidate_photo" class="form-control"/>
                <?php if (isset($candidate) && $candidate['candidate_photo']) { ?>
                    <small>If you don't want to change the photo, leave it blank.</small><br>
                    <img src="<?php echo $candidate['candidate_photo']; ?>" alt="Current Photo" width="100">
                <?php } ?>
            </div>
            <div class="form-group">
                <input type="text" name="candidate_details" placeholder="Candidate details" class="form-control" required value="<?php echo isset($candidate) ? htmlspecialchars($candidate['candidate_details']) : ''; ?>"/>
            </div>
            <input type="submit" value="<?php echo isset($candidate) ? 'Update Candidate' : 'Add Candidate'; ?>" name="<?php echo isset($candidate) ? 'editCandidateBtn' : 'addCandidateBtn'; ?>" class="btn btn-success">
        </form>
    </div>

    <div class="col-8">
        <h3>Candidates Details</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Photo</th>
                    <th scope="col">Candidate Name</th>
                    <th scope="col">Details</th>
                    <th scope="col">Election</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fetchingData = mysqli_query($db, "SELECT * FROM candidate_details") or die(mysqli_error($db));
                if (mysqli_num_rows($fetchingData) > 0) {
                    $sno = 1;
                    while ($row = mysqli_fetch_assoc($fetchingData)) {
                        $election_id = $row['election_id'];
                        $fetchingElectionName = mysqli_query($db, "SELECT * FROM elections WHERE id='" . $election_id . "'") or die(mysqli_error($db));
                        $execFetchingElectionNameQuery = mysqli_fetch_assoc($fetchingElectionName);
                        $election_name = $execFetchingElectionNameQuery['election_topic'];
                        $candidate_photo = $row['candidate_photo'];
                        ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td><img src="<?php echo $candidate_photo; ?>" class="candidate_photo"/></td>
                            <td><?php echo $row['candidate_name']; ?></td>
                            <td><?php echo $row['candidate_details']; ?></td>
                            <td><?php echo $election_name; ?></td>
                            <td>
                                <a href="addCandidate.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="addCandidate.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this candidate?')">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="7">No Candidate is Added.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Handling Candidate Addition or Update
if (isset($_POST["addCandidateBtn"]) || isset($_POST["editCandidateBtn"])) {
    $election_id = mysqli_real_escape_string($db, $_POST['election_id']);
    $candidate_name = mysqli_real_escape_string($db, $_POST['candidate_name']);
    $candidate_details = mysqli_real_escape_string($db, $_POST['candidate_details']);
    $inserted_by = $_SESSION['username'];
    $inserted_on = date("Y-m-d");

    $targetted_folder = "../assets/images/candidate_photos";
    $candidate_photo = isset($_FILES['candidate_photo']['name']) ? $targetted_folder . rand(1111111111, 9999999999) . "_" . rand(1111111111, 9999999999) . $_FILES['candidate_photo']['name'] : '';
    $candidate_photo_tmp_name = isset($_FILES['candidate_photo']['tmp_name']) ? $_FILES['candidate_photo']['tmp_name'] : '';
    $candidate_photo_type = strtolower(pathinfo($candidate_photo, PATHINFO_EXTENSION));
    $allowed_types = array("jpg", "png", "jpeg");
    $image_size = isset($_FILES['candidate_photo']['size']) ? $_FILES['candidate_photo']['size'] : 0;

    if ($image_size < 2000000 && $candidate_photo != '') {
        if (in_array($candidate_photo_type, $allowed_types)) {
            if (move_uploaded_file($candidate_photo_tmp_name, $candidate_photo)) {
                // If editing, update the candidate details
                if (isset($_GET['id'])) {
                    $candidate_id = $_GET['id'];
                    // Handle file deletion for update
                    if ($candidate['candidate_photo'] && file_exists($candidate['candidate_photo'])) {
                        unlink($candidate['candidate_photo']);
                    }
                    mysqli_query($db, "UPDATE candidate_details SET election_id='$election_id', candidate_name='$candidate_name', candidate_details='$candidate_details', candidate_photo='$candidate_photo' WHERE id='$candidate_id'") or die(mysqli_error($db));
                    echo "<script> location.assign('addCandidate.php?id=$candidate_id&editSuccess=1');</script>";
                } else {
                    // If adding new, insert the new candidate details
                    mysqli_query($db, "INSERT INTO candidate_details(election_id, candidate_name, candidate_details, candidate_photo, inserted_by, inserted_on) VALUES('$election_id', '$candidate_name', '$candidate_details', '$candidate_photo', '$inserted_by', '$inserted_on')") or die(mysqli_error($db));
                    echo "<script> location.assign('index.php?added=1');</script>";
                }
            } else {
                echo "<script> location.assign('index.php?failed=1');</script>";
            }
        } else {
            echo "<script> location.assign('index.php?invalidFile=1');</script>";
        }
    } else if ($image_size > 0) {
        echo "<script> location.assign('index.php?largeFile=1');</script>";
    } else {
        // If no photo is uploaded (for editing), proceed with the update
        if (isset($_GET['id'])) {
            $candidate_id = $_GET['id'];
            mysqli_query($db, "UPDATE candidate_details SET election_id='$election_id', candidate_name='$candidate_name', candidate_details='$candidate_details' WHERE id='$candidate_id'") or die(mysqli_error($db));
            echo "<script> location.assign('addCandidate.php?id=$candidate_id&editSuccess=1');</script>";
        } else {
            mysqli_query($db, "INSERT INTO candidate_details(election_id, candidate_name, candidate_details, inserted_by, inserted_on) VALUES('$election_id', '$candidate_name', '$candidate_details', '$inserted_by', '$inserted_on')") or die(mysqli_error($db));
            echo "<script> location.assign('index.php?added=1');</script>";
        }
    }
}
