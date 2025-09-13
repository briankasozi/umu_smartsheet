<?php
// views/project_form.php - Project form template
?>
<form method="post">
    <label>Name: <input type="text" name="name" value="<?= isset($project['name']) ? htmlspecialchars($project['name']) : '' ?>" required></label><br>
    <label>Description: <textarea name="description"><?= isset($project['description']) ? htmlspecialchars($project['description']) : '' ?></textarea></label><br>
    <label>Start Date: <input type="date" name="start_date" value="<?= isset($project['start_date']) ? $project['start_date'] : '' ?>"></label><br>
    <label>End Date: <input type="date" name="end_date" value="<?= isset($project['end_date']) ? $project['end_date'] : '' ?>"></label><br>
    <label>Status: <input type="text" name="status" value="<?= isset($project['status']) ? htmlspecialchars($project['status']) : '' ?>"></label><br>
    <label>Priority: <input type="text" name="priority" value="<?= isset($project['priority']) ? htmlspecialchars($project['priority']) : '' ?>"></label><br>
    <hr>
    <h3>Assign Tasks</h3>
    <div id="tasksContainer">
        <!-- Task assignment UI is handled in project_create.php -->
    </div>
    <button type="submit"><?= isset($project) ? 'Update' : 'Create' ?></button>
</form>
