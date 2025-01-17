<?php
ob_start();

if (isset($_GET['added'])) {
    ?>
    <div class="alert alert-success my-3" role="alert">
        Election has been added successfully.
    </div>
    <?php
} elseif (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($db, $_GET['delete_id']);
    mysqli_query($db, "DELETE FROM elections WHERE id = '$delete_id'") or die(mysqli_error($db));
    ?>
    <div class="alert alert-danger my-3" role="alert">
        Election has been deleted successfully.
    </div>
    <?php
}

if (isset($_GET['edit_id'])) {
    $election_id = $_GET['edit_id']; 
    $electionQuery = mysqli_query($db, "SELECT * FROM elections WHERE id = '$election_id'") or die(mysqli_error($db));
    $election = mysqli_fetch_assoc($electionQuery);
}
?>

<div class="row mt-3 my-3">
    <div class="col-4">
        <h3><?php echo isset($election) ? 'Edit Election' : 'Add New Election'; ?></h3>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="election_topic" placeholder="Election Topic" class="form-control" 
                       value="<?php echo isset($election['election_topic']) ? $election['election_topic'] : ''; ?>" required/>
            </div>
            <div class="form-group">
                <input type="number" name="number_of_candidates" placeholder="No of candidates" class="form-control" 
                       value="<?php echo isset($election['no_of_candidates']) ? $election['no_of_candidates'] : ''; ?>" required/>
            </div>
            <div class="form-group">
                <input type="date" name="starting_date" placeholder="Starting Date" class="form-control" 
                       value="<?php echo isset($election['starting_date']) ? $election['starting_date'] : ''; ?>" required/>
            </div>
            <div class="form-group">
                <input type="date" name="ending_date" placeholder="Ending Date" class="form-control" 
                       value="<?php echo isset($election['ending_date']) ? $election['ending_date'] : ''; ?>" required/>
            </div>

            <input type="hidden" name="election_id" value="<?php echo isset($election['id']) ? $election['id'] : ''; ?>" />
            <input type="submit" value="<?php echo isset($election['id']) ? 'Update Election' : 'Add Election'; ?>" 
                   name="<?php echo isset($election['id']) ? 'updateElectionBtn' : 'addElectionBtn'; ?>" class="btn btn-success">
        </form>
    </div>

    <div class="col-8">
        <h3>Upcoming Elections</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Election Name</th>
                    <th scope="col"># Candidates</th>
                    <th scope="col">Starting Date</th>
                    <th scope="col">Ending Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $fetchingData = mysqli_query($db, "SELECT * FROM elections") or die(mysqli_error($db));
                $isAnyElectionAdded = mysqli_num_rows($fetchingData);
                if ($isAnyElectionAdded > 0) {
                    $sno = 1;
                    while ($row = mysqli_fetch_assoc($fetchingData)) {
                        $election_id = $row['id'];
                        ?>
                        <tr>
                            <td><?php echo $sno++; ?> </td>
                            <td><?php echo $row['election_topic']; ?> </td>
                            <td><?php echo $row['no_of_candidates']; ?> </td>
                            <td><?php echo $row['starting_date']; ?> </td>
                            <td><?php echo $row['ending_date']; ?> </td>
                            <td><?php echo $row['status']; ?> </td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="EditData(<?php echo $election_id; ?>)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="DeleteData(<?php echo $election_id; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="7">No election is added yet.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const DeleteData = (e_id) => {
        let c = confirm("Do you really want to delete it?");
        if (c === true) {
            location.assign("index.php?addElectionPage=1&delete_id=" + e_id);
        }
    };

    const EditData = (c_id) => {
        let edit = confirm("Do you want to edit the data?");
        if (edit === true) {
            location.assign("index.php?addElectionPage=1&edit_id=" + c_id);
        }
    };
</script>

<?php
if (isset($_POST["addElectionBtn"])) {
    $election_topic = mysqli_real_escape_string($db, $_POST['election_topic']);
    $number_of_candidates = mysqli_real_escape_string($db, $_POST['number_of_candidates']);
    $starting_date = mysqli_real_escape_string($db, $_POST['starting_date']);
    $ending_date = mysqli_real_escape_string($db, $_POST['ending_date']);
    $inserted_by = $_SESSION['username'];
    $inserted_on = date("Y-m-d");

    $date1 = date_create($inserted_on);
    $date2 = date_create($starting_date);
    $diff = date_diff($date1, $date2);

    $status = $diff->format("%R%a") > 0 ? "Inactive" : "Active";

    $query = "INSERT INTO elections (election_topic, no_of_candidates, starting_date, ending_date, status, inserted_by, inserted_on)
              VALUES ('$election_topic', '$number_of_candidates', '$starting_date', '$ending_date', '$status', '$inserted_by', '$inserted_on')";

    mysqli_query($db, $query) or die(mysqli_error($db));

    header("Location: index.php?addElectionPage=1&added=1");
    exit();
}

if (isset($_POST['updateElectionBtn'])) {
    $election_topic = mysqli_real_escape_string($db, $_POST['election_topic']);
    $number_of_candidates = mysqli_real_escape_string($db, $_POST['number_of_candidates']);
    $starting_date = mysqli_real_escape_string($db, $_POST['starting_date']);
    $ending_date = mysqli_real_escape_string($db, $_POST['ending_date']);
    
    
    $election_id = isset($_POST['election_id']) ? mysqli_real_escape_string($db, $_POST['election_id']) : null;

    if ($election_id) {
        $updateQuery = "UPDATE elections
                        SET election_topic = '$election_topic',
                            no_of_candidates = '$number_of_candidates',
                            starting_date = '$starting_date',
                            ending_date = '$ending_date'
                        WHERE id = '$election_id'";

        mysqli_query($db, $updateQuery) or die(mysqli_error($db));

        header("Location: index.php?addElectionPage=1&updated=1");
        exit();
    } else {
        echo "Election ID is not set."; // Ideally, handle this case more gracefully
    }
}

// Flush output buffer
ob_end_flush();
?>
