<?php
// Загрузка переменных из .env
require __DIR__ . '/load_env.php'; 

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];
$authUsername = $_ENV['AUTH_USERNAME'];
$authPassword = $_ENV['AUTH_PASSWORD'];

// Защита через HTTP-авторизацию
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $authUsername || $_SERVER['PHP_AUTH_PW'] !== $authPassword) {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

// Создание соединения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Имя файла для экспорта
$filename = "contacts_export_" . date('Ymd') . ".csv";

// Установка заголовков для загрузки файла
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=' . $filename);

// Открытие потока вывода
$output = fopen('php://output', 'w');

// Запись заголовков столбцов
fputcsv($output, array('ID', 'Name', 'Email', 'Created At'));

// Извлечение данных из таблицы
$query = "SELECT id, name, email, created_at FROM contacts";
$result = $conn->query($query);

// Запись данных в CSV файл
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
  }
}

// Закрытие соединения с базой данных
fclose($output);
$conn->close();
