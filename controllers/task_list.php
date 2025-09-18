<?php
// controllers/task_list.php - List Tasks for a Project
require_once '../config/db.php';
require_once '../models/Task.php';
if (!isset($_GET['project_id'])) {
    header('Location: ../index.php');
    exit();
}
$project_id = (int)$_GET['project_id'];
$tasks = getTasksByProject($project_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasks</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php $active = 'tasks'; include '../views/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Tasks</h2>
            <div class="space-x-2">
                <a href="project_sheet.php?project_id=<?= $project_id ?>" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 px-4 py-2 rounded shadow">Project Sheet</a>
                <a href="task_create.php?project_id=<?= $project_id ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Create Task</a>
            </div>
        </div>
        <!-- Filters -->
        <div class="flex flex-wrap gap-4 mb-4">
            <input id="filterTitle" type="text" placeholder="Filter by exact title..." class="border px-3 py-2 rounded w-48">
            <select id="filterStatus" class="border px-3 py-2 rounded">
                <option value="">All Statuses</option>
                <option value="Completed">Completed</option>
                <option value="In Progress">In Progress</option>
                <option value="Pending">Pending</option>
            </select>
            <select id="filterPerson" class="border px-3 py-2 rounded">
                <option value="">All Responsible Persons</option>
                <?php
                $allAssignees = [];
                foreach ($tasks as $task) {
                    if (!empty($task['assignees'])) {
                        foreach ($task['assignees'] as $assignee) {
                            $allAssignees[$assignee['username']] = true;
                        }
                    }
                }
                ksort($allAssignees);
                foreach (array_keys($allAssignees) as $username): ?>
                    <option value="<?= htmlspecialchars($username) ?>"><?= htmlspecialchars($username) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="overflow-x-auto rounded-lg shadow">
        <table id="tasksTable" class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b text-left">#</th>
                <th class="px-4 py-2 border-b text-left">Title</th>
                <th class="px-4 py-2 border-b text-left">Description</th>
                <th class="px-4 py-2 border-b text-left">Responsible Person(s)</th>
                <th class="px-4 py-2 border-b text-left">Status</th>
                <th class="px-4 py-2 border-b text-left">Due Date</th>
                <th class="px-4 py-2 border-b text-left">Progress</th>
                <th class="px-4 py-2 border-b text-left">Actions</th>
            </tr>
            </thead>
            <tbody id="tasksTableBody">
            <?php $num = 1; foreach ($tasks as $task): ?>
            <tr class="hover:bg-gray-50" data-title="<?= htmlspecialchars(strtolower($task['title'])) ?>">
                <td class="px-4 py-2 border-b text-gray-700 font-semibold"><?= $num++ ?></td>
                <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($task['title']) ?></td>
                <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($task['description']) ?></td>
                <td class="px-4 py-2 border-b text-gray-700">
                    <?php if (!empty($task['assignees'])): ?>
                        <?php foreach ($task['assignees'] as $assignee): ?>
                            <span class="inline-block bg-gray-200 text-gray-800 rounded px-2 py-1 text-xs mr-1 mb-1"><?= htmlspecialchars($assignee['username']) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-gray-400 italic">Unassigned</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-2 border-b">
                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                        <?php if ($task['status'] === 'Completed') echo 'bg-green-100 text-green-800';
                        elseif ($task['status'] === 'In Progress') echo 'bg-yellow-100 text-yellow-800';
                        else echo 'bg-gray-200 text-gray-700'; ?>"><?= htmlspecialchars($task['status']) ?></span>
                </td>
                <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($task['due_date']) ?></td>
                <td class="px-4 py-2 border-b">
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-500 h-4 rounded-full" style="width: <?= $task['progress'] ?>%"></div>
                    </div>
                    <span class="text-xs text-gray-600 ml-1"><?= $task['progress'] ?>%</span>
                </td>
                <td class="px-4 py-2 border-b">
                    <a href="task_edit.php?id=<?= $task['id'] ?>" class="text-blue-600 hover:underline mr-2">Edit</a>
                    <a href="task_delete.php?id=<?= $task['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this task?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4" id="paginationControls"></div>
        <a href="../index.php" class="inline-block mt-6 text-blue-700 hover:underline">&larr; Back to Dashboard</a>
    </main>
</div>
    <!-- Mobile menu button (optional) -->
    <script>
    // Pagination and filtering logic
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredRows = Array.from(document.querySelectorAll('#tasksTableBody tr'));

    function renderTable() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        filteredRows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? '' : 'none';
        });
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        let html = '';
        if (totalPages > 1) {
            html += `<button onclick="gotoPage(1)" class="px-2 py-1 mx-1 rounded ${currentPage === 1 ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">First</button>`;
            html += `<button onclick="gotoPage(${currentPage-1})" class="px-2 py-1 mx-1 rounded ${currentPage === 1 ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">Prev</button>`;
            for (let i = 1; i <= totalPages; i++) {
                html += `<button onclick="gotoPage(${i})" class="px-2 py-1 mx-1 rounded ${currentPage === i ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200'}">${i}</button>`;
            }
            html += `<button onclick="gotoPage(${currentPage+1})" class="px-2 py-1 mx-1 rounded ${currentPage === totalPages ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">Next</button>`;
            html += `<button onclick="gotoPage(${totalPages})" class="px-2 py-1 mx-1 rounded ${currentPage === totalPages ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">Last</button>`;
        }
        document.getElementById('paginationControls').innerHTML = html;
    }

    function gotoPage(page) {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    }

    function filterTable() {
        const titleVal = document.getElementById('filterTitle').value.trim().toLowerCase();
        const statusVal = document.getElementById('filterStatus').value;
        const personVal = document.getElementById('filterPerson').value;
        filteredRows = Array.from(document.querySelectorAll('#tasksTableBody tr')).filter(row => {
            const title = row.children[1].textContent.trim().toLowerCase();
            const status = row.children[4].textContent.trim();
            const persons = Array.from(row.children[3].querySelectorAll('span')).map(s => s.textContent.trim());
            let titleMatch = !titleVal || title === titleVal;
            let statusMatch = !statusVal || status === statusVal;
            let personMatch = !personVal || persons.includes(personVal);
            return titleMatch && statusMatch && personMatch;
        });
        currentPage = 1;
        renderTable();
    }

    document.getElementById('filterTitle').addEventListener('input', filterTable);
    document.getElementById('filterStatus').addEventListener('change', filterTable);
    document.getElementById('filterPerson').addEventListener('change', filterTable);
    // Initial render
    filterTable();
    </script>

    <script>
    // Pagination and filtering logic
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredRows = Array.from(document.querySelectorAll('#tasksTableBody tr'));

    function renderTable() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        filteredRows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? '' : 'none';
        });
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        let html = '';
        if (totalPages > 1) {
            html += `<button onclick="gotoPage(1)" class="px-2 py-1 mx-1 rounded ${currentPage === 1 ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">First</button>`;
            html += `<button onclick="gotoPage(${currentPage-1})" class="px-2 py-1 mx-1 rounded ${currentPage === 1 ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">Prev</button>`;
            for (let i = 1; i <= totalPages; i++) {
                html += `<button onclick="gotoPage(${i})" class="px-2 py-1 mx-1 rounded ${currentPage === i ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200'}">${i}</button>`;
            }
            html += `<button onclick="gotoPage(${currentPage+1})" class="px-2 py-1 mx-1 rounded ${currentPage === totalPages ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">Next</button>`;
            html += `<button onclick="gotoPage(${totalPages})" class="px-2 py-1 mx-1 rounded ${currentPage === totalPages ? 'bg-gray-300' : 'bg-gray-100 hover:bg-gray-200'}">Last</button>`;
        }
        document.getElementById('paginationControls').innerHTML = html;
    }

    function gotoPage(page) {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    }

    function filterTable() {
        const titleVal = document.getElementById('filterTitle').value.toLowerCase();
        const statusVal = document.getElementById('filterStatus').value;
        filteredRows = Array.from(document.querySelectorAll('#tasksTableBody tr')).filter(row => {
            const title = row.children[1].textContent.toLowerCase();
            const status = row.children[3].textContent;
            return (!titleVal || title.includes(titleVal)) && (!statusVal || status === statusVal);
        });
        currentPage = 1;
        renderTable();
    }

    // Initial render
    filterTable();
    </script>
</body>
</html>
