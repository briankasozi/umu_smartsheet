
<?php
$active = 'calendar';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Calendar | TagDev2.0</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="../public/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		.sidebar-main {
			transition: width 0.3s ease;
			width: 15rem;
			position: fixed;
			top: 0;
			left: 0;
			bottom: 0;
			z-index: 40;
		}
		.sidebar-main.collapsed {
			width: 4rem;
		}
		.menu-item {
			display: flex;
			align-items: center;
			padding: 0.75rem 1rem;
			color: white;
			text-decoration: none;
			border-radius: 0.5rem;
			margin: 0.25rem 0.5rem;
			transition: all 0.2s;
		}
		.menu-item:hover {
			background-color: rgba(255, 255, 255, 0.1);
		}
		.menu-item.active {
			background-color: rgba(255, 255, 255, 0.2);
		}
		.menu-item svg {
			min-width: 1.5rem;
			width: 1.5rem;
			height: 1.5rem;
		}
		.menu-item-text {
			margin-left: 0.75rem;
			transition: opacity 0.2s;
		}
		.collapsed .menu-item-text {
			opacity: 0;
			width: 0;
			margin-left: 0;
		}
		.notification-badge {
			position: absolute;
			top: 0;
			right: 0;
			transform: translate(25%, -25%);
			background-color: #ea4335;
			color: white;
			border-radius: 9999px;
			padding: 0.125rem 0.375rem;
			font-size: 0.75rem;
			font-weight: 500;
		}
		.calendar-table th, .calendar-table td {
			border: 1px solid #e5e7eb;
			padding: 10px;
			text-align: center;
		}
		.calendar-table th {
			background: #f3f4f6;
			color: #2563eb;
			font-weight: 600;
		}
		.calendar-table td {
			background: #fff;
		}
		.calendar-today {
			background: #2563eb;
			color: #fff;
			border-radius: 8px;
		}
	</style>
</head>
<body class="bg-gray-50">
	<!-- Main Sidebar -->
	<?php include_once '../views/sidebar.php'; ?>
	<!-- Secondary Sidebar -->
	<div id="secondarySidebar" class="fixed top-0 bottom-0 w-64 bg-white border-r border-gray-200 z-30" style="left: 15rem;">
		<div class="p-4">
			<h1 class="text-lg font-medium mb-4">Calendar</h1>
			<button class="w-full bg-[#1a73e8] text-white rounded-lg px-4 py-2 font-medium hover:bg-blue-700 mb-4">+ Add Event</button>
			<div class="space-y-2">
				<div class="text-sm font-medium text-gray-500 mb-2">Workspace items</div>
				<a href="#" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100">
					<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
					</svg>
					<span class="text-gray-700">Calendar Dashboard</span>
				</a>
				<a href="#" class="flex items-center gap-2 p-2 rounded-lg bg-blue-50">
					<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
					</svg>
					<span class="text-blue-600">Event Sheet</span>
				</a>
			</div>
		</div>
	</div>
	<!-- Main Content -->
	<div id="mainContent" class="pl-[31rem] pr-4 pt-4">
		<div class="bg-white rounded-lg shadow p-6 mb-6">
			<h2 class="text-2xl font-semibold text-gray-800">Calendar</h2>
		</div>
		<section style="width:100%; max-width:900px; background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.08); padding:2.5rem 2rem; margin-bottom:2rem;">
			<div style="overflow-x:auto;">
				<?php
				// Simple calendar for current month
				$month = date('n');
				$year = date('Y');
				$today = date('j');
				$firstDayOfMonth = date('N', strtotime("$year-$month-01"));
				$daysInMonth = date('t');
				$monthName = date('F');
				?>
				<table class="calendar-table" style="width:100%; border-collapse:collapse; margin-bottom:2rem;">
					<thead>
						<tr>
							<th colspan="7" style="font-size:1.3rem; text-align:center; background:#e0e7ff; color:#3730a3; padding:16px 0; border-radius:8px 8px 0 0;"> <?= $monthName . ' ' . $year ?> </th>
						</tr>
						<tr>
							<th>Mon</th>
							<th>Tue</th>
							<th>Wed</th>
							<th>Thu</th>
							<th>Fri</th>
							<th>Sat</th>
							<th>Sun</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<?php
						$day = 1;
						$cell = 1;
						// Print empty cells for days before the first
						for ($i = 1; $i < $firstDayOfMonth; $i++, $cell++) {
							echo '<td></td>';
						}
						while ($day <= $daysInMonth) {
							if ($cell > 7) {
								echo '</tr><tr>';
								$cell = 1;
							}
							$isToday = ($day == $today);
							echo '<td'.($isToday ? ' class="calendar-today"':'').'>'.$day.'</td>';
							$day++;
							$cell++;
						}
						// Fill the last row
						while ($cell <= 7) {
							echo '<td></td>';
							$cell++;
						}
						?>
						</tr>
					</tbody>
				</table>
				<div style="text-align:center; margin-top:1.5rem;">
					<div style="display:inline-block; background:#f3f4f6; color:#2563eb; padding:1rem 2rem; border-radius:8px; font-size:1.1rem; font-weight:500;">No events for this month.</div>
				</div>
			</div>
		</section>
	</div>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const mainSidebar = document.getElementById('mainSidebar');
		const secondarySidebar = document.getElementById('secondarySidebar');
		const mainContent = document.getElementById('mainContent');
		const sidebarToggle = document.getElementById('sidebarToggle');

		let isCollapsed = false;

		function updateLayout() {
			if (isCollapsed) {
				mainSidebar.classList.add('collapsed');
				secondarySidebar.style.left = '4rem';
				mainContent.style.paddingLeft = '20rem';
			} else {
				mainSidebar.classList.remove('collapsed');
				secondarySidebar.style.left = '15rem';
				mainContent.style.paddingLeft = '31rem';
			}
		}

		sidebarToggle.addEventListener('click', () => {
			isCollapsed = !isCollapsed;
			updateLayout();
		});
	});
	</script>
</body>
</html>
