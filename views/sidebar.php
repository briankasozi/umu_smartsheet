<?php
// views/sidebar.php - Modern Smartsheet-style sidebar
if (!isset($active)) { $active = ''; }
function item_class($key, $active) {
    if ($key === $active) return 'flex items-center gap-2 px-2 py-2 rounded-md bg-blue-50 text-blue-700 font-medium';
    return 'flex items-center gap-2 px-2 py-2 rounded-md text-gray-700 hover:bg-gray-50 font-medium';
}
?>
<aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 pt-16 flex flex-col">
    <div class="flex-1 px-3 py-4 space-y-6">
        <div class="space-y-2">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">Navigation</div>
            <nav class="space-y-1">
                <a href="<?= strpos($_SERVER['PHP_SELF'], '/controllers/') !== false ? '../index.php' : 'index.php' ?>" 
                   class="<?= item_class('projects', $active) ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    Home
                </a>
                <a href="<?= strpos($_SERVER['PHP_SELF'], '/controllers/') !== false ? '../controllers/task_list.php?project_id=1' : 'controllers/task_list.php?project_id=1' ?>" 
                   class="<?= item_class('tasks', $active) ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Tasks
                </a>
                <a href="<?= strpos($_SERVER['PHP_SELF'], '/controllers/') !== false ? '../controllers/user_list.php' : 'controllers/user_list.php' ?>" 
                   class="<?= item_class('users', $active) ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Team
                </a>
                <a href="<?= strpos($_SERVER['PHP_SELF'], '/controllers/') !== false ? '../controllers/project_create.php' : 'controllers/project_create.php' ?>" 
                   class="<?= item_class('create_project', $active) ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Project
                </a>
            </nav>
        </div>

        <div class="space-y-2">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">Tools</div>
            <nav class="space-y-1">
                <a href="#" class="<?= item_class('deadlines', $active) ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Timeline
                </a>
                <a href="#" class="<?= item_class('analytics', $active) ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Reports
                </a>
                <a href="#" class="<?= item_class('notifications', $active) ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notifications
                </a>
            </nav>
        </div>
    </div>
</aside>


