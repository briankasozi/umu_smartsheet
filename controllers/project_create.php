<?php
// controllers/project_create.php - Create Project
session_start();
require_once '../config/db.php';
$error = '';
require_once '../models/Task.php';
require_once '../models/User.php';
// Fetch all users for assignment
$all_users = getAllUsers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name']);
	$description = trim($_POST['description']);
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$status = $_POST['status'];
	$priority = $_POST['priority'];
	$tasks = isset($_POST['tasks']) ? $_POST['tasks'] : [];
	if (!$name) {
		$error = 'Project name is required.';
	} else {
		$stmt = $pdo->prepare('INSERT INTO projects (name, description, start_date, end_date, status, priority) VALUES (?, ?, ?, ?, ?, ?)');
		$stmt->execute([$name, $description, $start_date, $end_date, $status, $priority]);
		$project_id = $pdo->lastInsertId();
		// Insert tasks if provided
		foreach ($tasks as $task) {
			if (!empty($task['title'])) {
				$assignees = isset($task['assignees']) ? $task['assignees'] : [];
				createTask([
					'project_id' => $project_id,
					'title' => $task['title'],
					'description' => $task['description'],
					'status' => $task['status'],
					'due_date' => $task['due_date'],
					'progress' => $task['progress'],
					'agreed_date' => isset($task['agreed_date']) ? $task['agreed_date'] : null,
					'actual_date' => isset($task['actual_date']) ? $task['actual_date'] : null,
					'notes' => isset($task['notes']) ? $task['notes'] : null,
					'assignees' => $assignees
				]);
			}
		}
		header('Location: ../index.php');
		exit();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Create Project</title>
	<link rel="stylesheet" href="../public/style.css">
	<script src="https://cdn.tailwindcss.com"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		.task-card{border:1px solid #e5e7eb}
	</style>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
	<?php $active = 'create_project'; include '../views/sidebar.php'; ?>

	<main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
		<div class="w-full flex justify-center">
			<div class="w-full max-w-3xl">
				<div class="flex items-center justify-between mb-6">
					<h2 class="text-2xl md:text-3xl font-bold text-gray-900">Create Project</h2>
					<a href="../index.php" class="text-blue-700 hover:underline">Back to Dashboard</a>
				</div>
				<?php if ($error): ?><p class="mb-4 text-red-600"><?= $error ?></p><?php endif; ?>
				<p class="text-gray-600 mb-4">Provide core details, then add initial tasks and assignees. You can edit anytime.</p>

				<form method="post" id="projectForm" class="bg-white rounded-lg shadow-lg ring-1 ring-gray-100 p-6 space-y-8">
					<h3 class="text-lg font-semibold text-gray-800">Project Details</h3>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<label class="block">
							<span class="block text-sm font-medium text-gray-700 mb-1">Name</span>
							<input type="text" name="name" placeholder="e.g., 2025 Strategic Plan" required class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
							<span class="text-xs text-gray-500">Give your project a clear, recognizable name.</span>
						</label>
						<label class="block md:col-span-2">
							<span class="block text-sm font-medium text-gray-700 mb-1">Description</span>
							<textarea name="description" rows="3" placeholder="Brief summary, goals, context..." class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
						</label>
						<label class="block">
							<span class="block text-sm font-medium text-gray-700 mb-1">Start Date</span>
							<input type="date" name="start_date" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
						</label>
						<label class="block">
							<span class="block text-sm font-medium text-gray-700 mb-1">End Date</span>
							<input type="date" name="end_date" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
						</label>
						<label class="block">
							<span class="block text-sm font-medium text-gray-700 mb-1">Status</span>
							<select name="status" class="border border-gray-300 rounded px-3 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
								<option value="">Select status</option>
								<option>Planned</option>
								<option>In Progress</option>
								<option>On Hold</option>
								<option>Completed</option>
							</select>
						</label>
						<label class="block">
							<span class="block text-sm font-medium text-gray-700 mb-1">Priority</span>
							<select name="priority" class="border border-gray-300 rounded px-3 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
								<option value="">Select priority</option>
								<option>Low</option>
								<option>Medium</option>
								<option>High</option>
								<option>Critical</option>
							</select>
						</label>
					</div>

					<div class="pt-2">
						<div class="flex items-center justify-between">
							<h3 class="text-xl font-semibold text-gray-800">Assign Tasks</h3>
							<button type="button" onclick="addTaskBlock()" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Add Task</button>
						</div>
						<p class="text-sm text-gray-500 mt-1">Create as many tasks as needed. Assign multiple users if required.</p>
						<div id="tasksContainer" class="mt-4 space-y-4">
							<div class="task-block bg-white rounded-lg task-card ring-1 ring-gray-200 p-4">
								<div class="flex items-center justify-between mb-4">
									<div class="text-sm font-semibold text-gray-700">Task #1</div>
									<button type="button" onclick="this.closest('.task-block').remove()" class="text-red-600 hover:underline">Remove</button>
								</div>
								<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
									<label class="block">
										<span class="block text-sm font-medium text-gray-700 mb-1">Title</span>
										<input type="text" name="tasks[0][title]" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
									</label>
									<label class="block">
										<span class="block text-sm font-medium text-gray-700 mb-1">Description</span>
										<input type="text" name="tasks[0][description]" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
									</label>
									<label class="block">
										<span class="block text-sm font-medium text-gray-700 mb-1">Status</span>
										<select name="tasks[0][status]" class="border border-gray-300 rounded px-3 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
											<option value="">Select status</option>
											<option>Pending</option>
											<option>In Progress</option>
											<option>Blocked</option>
											<option>Completed</option>
										</select>
									</label>
									<label class="block">
										<span class="block text-sm font-medium text-gray-700 mb-1">Due Date</span>
										<input type="date" name="tasks[0][due_date]" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
									</label>
									<label class="block">
										<span class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</span>
										<input type="number" name="tasks[0][progress]" min="0" max="100" value="0" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
									</label>
									<label class="block">
										<span class="block text-sm font-medium text-gray-700 mb-1">Agreed Date</span>
										<input type="date" name="tasks[0][agreed_date]" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
									</label>
									<label class="block">
										<span class="block text-sm font-medium text-gray-700 mb-1">Actual Date</span>
										<input type="date" name="tasks[0][actual_date]" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
									</label>
									<label class="block md:col-span-2">
										<span class="block text-sm font-medium text-gray-700 mb-1">Notes</span>
										<input type="text" name="tasks[0][notes]" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
									</label>
									<label class="block md:col-span-2">
										<span class="block text-sm font-medium text-gray-700 mb-1">Assign To</span>
										<select name="tasks[0][assignees][]" multiple size="3" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
											<?php foreach ($all_users as $user): ?>
												<option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role_name']) ?>)</option>
											<?php endforeach; ?>
										</select>
									</label>
								</div>
							</div>
						</div>
					</div>

					<div class="pt-2 flex justify-end">
						<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">Create Project</button>
					</div>
				</form>
			</div>
		</div>
	</main>
</div>

	<script>
	let taskIndex = 1;
	function addTaskBlock() {
		const container = document.getElementById('tasksContainer');
		const block = document.createElement('div');
		block.className = 'task-block bg-white rounded-lg task-card ring-1 ring-gray-200 p-4';
		let userOptions = `<?php foreach ($all_users as $user): ?>` +
			`<option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role_name']) ?>)</option>` +
		`<?php endforeach; ?>`;
		block.innerHTML = `
		<div class=\"flex items-center justify-between mb-4\">\n\
			<div class=\"text-sm font-semibold text-gray-700\">Task #${taskIndex + 1}</div>\n\
			<button type=\"button\" onclick=\"this.closest('.task-block').remove()\" class=\"text-red-600 hover:underline\">Remove</button>\n\
		</div>\n\
		<div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">\n\
			<label class=\"block\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Title</span>\n\
				<input type=\"text\" name=\"tasks[${taskIndex}][title]\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\" />\n\
			</label>\n\
			<label class=\"block\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Description</span>\n\
				<input type=\"text\" name=\"tasks[${taskIndex}][description]\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\" />\n\
			</label>\n\
			<label class=\"block\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Status</span>\n\
				<select name=\"tasks[${taskIndex}][status]\" class=\"border border-gray-300 rounded px-3 py-2 w-full bg-white focus:outline-none focus:ring-2 focus:ring-blue-500\">\n\
					<option value=\"\">Select status</option>\n\
					<option>Pending</option>\n\
					<option>In Progress</option>\n\
					<option>Blocked</option>\n\
					<option>Completed</option>\n\
				</select>\n\
			</label>\n\
			<label class=\"block\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Due Date</span>\n\
				<input type=\"date\" name=\"tasks[${taskIndex}][due_date]\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\" />\n\
			</label>\n\
			<label class=\"block\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Progress (%)</span>\n\
				<input type=\"number\" name=\"tasks[${taskIndex}][progress]\" min=\"0\" max=\"100\" value=\"0\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\" />\n\
			</label>\n\
			<label class=\"block\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Agreed Date</span>\n\
				<input type=\"date\" name=\"tasks[${taskIndex}][agreed_date]\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\" />\n\
			</label>\n\
			<label class=\"block\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Actual Date</span>\n\
				<input type=\"date\" name=\"tasks[${taskIndex}][actual_date]\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\" />\n\
			</label>\n\
			<label class=\"block md:col-span-2\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Notes</span>\n\
				<input type=\"text\" name=\"tasks[${taskIndex}][notes]\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\" />\n\
			</label>\n\
			<label class=\"block md:col-span-2\">\n\
				<span class=\"block text-sm font-medium text-gray-700 mb-1\">Assign To</span>\n\
				<select name=\"tasks[${taskIndex}][assignees][]\" multiple size=\"3\" class=\"border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500\">${userOptions}</select>\n\
			</label>\n\
		</div>\n\
		`;
		container.appendChild(block);
		taskIndex++;
	}
	</script>
</body>
</html>
