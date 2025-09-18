<?php
// views/projects.php - Projects table with filters and create button
require_once __DIR__ . '/../models/Project.php';

$projects = getAllProjects();

// Handle filter (simple example: by status or priority)
$filter_status = $_GET['status'] ?? '';
$filter_priority = $_GET['priority'] ?? '';
if ($filter_status || $filter_priority) {
	$projects = array_filter($projects, function($project) use ($filter_status, $filter_priority) {
		$status_match = $filter_status ? $project['status'] === $filter_status : true;
		$priority_match = $filter_priority ? $project['priority'] === $filter_priority : true;
		return $status_match && $priority_match;
	});
}

?>


<div>
	<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
		<form method="get" style="display: flex; gap: 1rem; background: #fff; padding: 10px 20px; border-radius: 5px; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
			<label style="display: flex; flex-direction: column; font-weight: 500;">
				Status
				<select name="status">
					<option value="">All</option>
					<option value="Active" <?= $filter_status==='Active'?'selected':'' ?>>Active</option>
					<option value="Completed" <?= $filter_status==='Completed'?'selected':'' ?>>Completed</option>
					<option value="On Hold" <?= $filter_status==='On Hold'?'selected':'' ?>>On Hold</option>
				</select>
			</label>
			<label style="display: flex; flex-direction: column; font-weight: 500;">
				Priority
				<select name="priority">
					<option value="">All</option>
					<option value="High" <?= $filter_priority==='High'?'selected':'' ?>>High</option>
					<option value="Medium" <?= $filter_priority==='Medium'?'selected':'' ?>>Medium</option>
					<option value="Low" <?= $filter_priority==='Low'?'selected':'' ?>>Low</option>
				</select>
			</label>
			<button type="submit" style="width:auto; margin-top: 22px;">Filter</button>
		</form>
		<a href="../controllers/project_create.php" style="padding: 10px 24px; background: #2563eb; color: #fff; border-radius: 4px; text-decoration: none; font-weight: bold; box-shadow: 0 1px 4px rgba(0,0,0,0.08);">+ Create New Project</a>
	</div>
	<div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 8px rgba(0,0,0,0.06); padding: 1rem;">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Description</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Status</th>
					<th>Priority</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($projects)): ?>
					<tr><td colspan="7" style="text-align:center; color:#888;">No projects found.</td></tr>
				<?php else: foreach ($projects as $project): ?>
					<tr>
						<td><?= htmlspecialchars($project['name']) ?></td>
						<td><?= htmlspecialchars($project['description']) ?></td>
						<td><?= htmlspecialchars($project['start_date']) ?></td>
						<td><?= htmlspecialchars($project['end_date']) ?></td>
						<td><span style="padding:2px 8px; border-radius:12px; background:#e0e7ff; color:#3730a3; font-size:0.95em;"><?= htmlspecialchars($project['status']) ?></span></td>
						<td><span style="padding:2px 8px; border-radius:12px; background:#fef3c7; color:#b45309; font-size:0.95em;"><?= htmlspecialchars($project['priority']) ?></span></td>
						<td>
							<a href="../controllers/project_edit.php?id=<?= $project['id'] ?>" style="margin-right:8px;">Edit</a>
							<a href="../controllers/project_delete.php?id=<?= $project['id'] ?>" onclick="return confirm('Delete this project?')" style="color:#dc2626;">Delete</a>
						</td>
					</tr>
				<?php endforeach; endif; ?>
			</tbody>
		</table>
	</div>
</div>
