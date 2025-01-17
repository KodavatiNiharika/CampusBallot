<form method="POST">
    <div class="form-group">
        <input type="text" name="election_topic" placeholder="Election Topic" class="form-control" value="<?php echo isset($candidate) ? $candidate['election_topic'] : ''; ?>" required/>
    </div>
    <div class="form-group">
        <input type="number" name="number_of_candidates" placeholder="No of candidates" class="form-control" value="<?php echo isset($candidate) ? $candidate['no_of_candidates'] : ''; ?>" required/>
    </div>
    <div class="form-group">
        <input type="text" onfocus="this.type='Date'" name="starting_date" placeholder="Starting Date" class="form-control" value="<?php echo isset($candidate) ? $candidate['starting_date'] : ''; ?>" required/>
    </div>
    <div class="form-group">
        <input type="text" onfocus="this.type='Date'" name="ending_date" placeholder="Ending Date" class="form-control" value="<?php echo isset($candidate) ? $candidate['ending_date'] : ''; ?>" required/>
    </div>
    
    <input type="hidden" name="candidate_id" value="<?php echo isset($candidate_id) ? $candidate_id : ''; ?>" />
    <input type="submit" value="Save Election" name="<?php echo isset($candidate_id) ? 'updateElectionBtn' : 'addElectionBtn'; ?>" class="btn btn-success">
</form>
