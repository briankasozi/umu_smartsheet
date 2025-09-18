<?php
// controllers/project_sheet.php - Spreadsheet-like project task view
require_once '../config/db.php';
require_once '../models/Task.php';
require_once '../models/User.php';

if (!isset($_GET['project_id'])) {
    header('Location: ../index.php');
    exit();
}
$project_id = (int)$_GET['project_id'];

// Fetch project
$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->execute([$project_id]);
$project = $stmt->fetch();
if (!$project) {
    header('Location: ../index.php');
    exit();
}

// Fetch tasks with assignees
$tasks = getTasksByProject($project_id);

function colorForStatus($status) {
    if ($status === 'Completed') return ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'dot' => 'bg-green-500'];
    if ($status === 'In Progress') return ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-400'];
    if ($status === 'Pending' || $status === 'Blocked') return ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500'];
    return ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'dot' => 'bg-gray-400'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Sheet</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <?php $active = 'projects'; include '../views/sidebar.php'; ?>
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <div class="flex gap-6">
            <aside class="hidden lg:block w-64">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">P</div>
                        <div>
                            <div class="text-sm text-gray-500">Project</div>
                            <div class="text-base font-semibold text-gray-900 truncate" title="<?= htmlspecialchars($project['name']) ?>"><?= htmlspecialchars($project['name']) ?></div>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-gray-700 mb-2">Workspace items</div>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-gray-700"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> <a class="hover:underline" href="project_sheet.php?project_id=<?= $project_id ?>">Project Sheet</a></li>
                        <li class="flex items-center gap-2 text-gray-700"><span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> <a class="hover:underline" href="task_list.php?project_id=<?= $project_id ?>">Task List</a></li>
                        <li class="flex items-center gap-2 text-gray-700"><span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> <a class="hover:underline" href="project_edit.php?id=<?= $project_id ?>">Project Dashboard</a></li>
                    </ul>
                </div>
            </aside>

            <section class="flex-1">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Project Sheet</h1>
                        <p class="text-gray-600">Overview of tasks</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="task_list.php?project_id=<?= $project_id ?>" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 px-3 py-2 rounded shadow-sm">Tasks</a>
                        <a href="project_edit.php?id=<?= $project_id ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Edit Project</a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <button class="px-2 py-1 rounded border border-gray-200 bg-white hover:bg-gray-100">Table</button>
                            <button class="px-2 py-1 rounded border border-gray-200 bg-white hover:bg-gray-100">Filter</button>
                            <button class="px-2 py-1 rounded border border-gray-200 bg-white hover:bg-gray-100">Format</button>
                            <button class="px-2 py-1 rounded border border-gray-200 bg-white hover:bg-gray-100">Formulas</button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input id="filterInput" type="text" placeholder="Filter tasks..." class="border px-3 py-1.5 rounded text-sm">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 border-b text-left text-sm font-semibold text-gray-700 w-14">#</th>
                                    <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Task</th>
                                    <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Assigned To</th>
                                    <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Condition</th>
                                    <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Start Date</th>
                                </tr>
                            </thead>
                            <tbody id="sheetBody">
                            <?php $row=1; foreach ($tasks as $task): $c = colorForStatus($task['status']); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 border-b text-gray-500 text-sm font-semibold"><?= $row++ ?></td>
                                    <td class="px-4 py-2 border-b text-gray-800 font-medium" data-col="task"><?= htmlspecialchars($task['title']) ?></td>
                                    <td class="px-4 py-2 border-b" data-col="status">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold <?= $c['bg'] . ' ' . $c['text'] ?>"><?= htmlspecialchars($task['status'] ?: 'Not Started') ?></span>
                                    </td>
                                    <td class="px-4 py-2 border-b" data-col="assignees">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <?php if (!empty($task['assignees'])): foreach ($task['assignees'] as $assignee): $initials = strtoupper(substr($assignee['username'],0,2)); ?>
                                                <span class="inline-flex items-center gap-2">
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xxs font-bold border border-white"><?= $initials ?></span>
                                                    <span class="text-sm text-gray-700 mr-2"><?= htmlspecialchars($assignee['username']) ?></span>
                                                </span>
                                            <?php endforeach; else: ?>
                                                <span class="text-gray-400 text-sm italic">Unassigned</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border-b" data-col="condition">
                                        <span class="inline-block w-3 h-3 rounded-full <?= $c['dot'] ?>"></span>
                                    </td>
                                    <td class="px-4 py-2 border-b text-gray-700" data-col="start">
                                        <?= htmlspecialchars($task['agreed_date'] ?: $task['due_date'] ?: '') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="../index.php" class="inline-block mt-6 text-blue-700 hover:underline">&larr; Back to Dashboard</a>
            </section>
        </div>
    </main>
</div>

<script>
const filterInput = document.getElementById('filterInput');
const rows = Array.from(document.querySelectorAll('#sheetBody tr'));
filterInput.addEventListener('input', () => {
    const q = filterInput.value.trim().toLowerCase();
    rows.forEach(row => {
        const t = row.querySelector('[data-col="task"]').textContent.toLowerCase();
        const s = row.querySelector('[data-col="status"]').textContent.toLowerCase();
        const a = row.querySelector('[data-col="assignees"]').textContent.toLowerCase();
        row.style.display = (!q || t.includes(q) || s.includes(q) || a.includes(q)) ? '' : 'none';
    });
});
</script>
</body>
</html>


