<?php
// debug.php - quick environment and routing checks
header('Content-Type: text/html; charset=utf-8');
echo "<pre>";
echo "SERVING FILE : ".__FILE__."\n";
echo "TIME         : ".date('c')."\n";
echo "DOC_ROOT     : ".(@$_SERVER['DOCUMENT_ROOT'] ?: '(unknown)')."\n";
echo "SCRIPT_NAME  : ".(@$_SERVER['SCRIPT_NAME'] ?: '(unknown)')."\n";
echo "REQUEST_URI  : ".(@$_SERVER['REQUEST_URI'] ?: '(unknown)')."\n";
echo "CWD          : ".getcwd()."\n\n";

// Check key files
$paths = [
    'index.php',
    'views/sidebar.php',
    'controllers/project_sheet.php',
    'controllers/task_list.php',
    'public/style.css',
];
foreach ($paths as $p) {
    $exists = file_exists(__DIR__ . DIRECTORY_SEPARATOR . $p);
    echo str_pad($p, 40) . ' : ' . ($exists ? 'FOUND  ' . realpath(__DIR__ . DIRECTORY_SEPARATOR . $p) : 'MISSING') . "\n";
}

echo "\nDirectory listing (project root):\n";
$list = scandir(__DIR__);
foreach ($list as $f) { if ($f === '.' || $f === '..') continue; echo " - $f\n"; }

echo "\nPHP OK: If you see this page, PHP is executing.\n";
echo "</pre>";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="bg-white rounded shadow p-4">
        <div class="text-gray-800">If this box has Tailwind styling, the CDN is reachable.</div>
    </div>
    <p class="mt-4"><a class="text-blue-700 underline" href="index.php">Go to index.php</a></p>
</body>
</html>


