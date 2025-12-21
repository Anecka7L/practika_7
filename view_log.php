<?php
session_start();
include("./settings/connect_datebase.php");
include("./settings/log_functions.php");

if (!isset($_SESSION['user']) || $_SESSION['user'] == -1) {
    header("Location: login.php");
    exit();
}

logToFile("Просмотр лог-файла", $_SESSION['user']);

$logFile = __DIR__ . '/log.txt';
$logContent = file_exists($logFile) ? file_get_contents($logFile) : 'Файл лога пуст или не существует.';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Просмотр лог-файла</title>
    <style>
        pre { background: #f5f5f5; padding: 20px; border: 1px solid #ddd; }
        .button { display: inline-block; margin: 10px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; }
    </style>
</head>
<body>
    <h2>Содержимое log.txt</h2>
    <pre><?php echo htmlspecialchars($logContent); ?></pre>
    <a href="logs.php" class="button">Назад к журналу</a>
    <a href="admin.php" class="button">В админ-панель</a>
</body>
</html>