<?php
// Загрузка переменных из .env
require __DIR__ . '/load_env.php'; 

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

// Создание соединения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Чтение и декодирование JSON из тела запроса
// $json = file_get_contents('php://input');
// $params = json_decode($json, true); // Декодирование JSON в ассоциативный массив

// Проверка, что JSON был успешно декодирован и поля не пустые
// if ($params && isset($params['name'], $params['email'])) {
//     $name = trim($params['name']);
//     $email = trim($params['email']);
//     ...

// Проверка, что данные переданы через POST и поля не пустые
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Проверка, что поля не пустые
    if (!empty($name) && !empty($email)) {
        // Подготовленный запрос для безопасной вставки данных
        $stmt = $conn->prepare("INSERT INTO contacts (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Fields cannot be empty.";
    }
} else {
    echo "Form was not submitted properly.";
}

$conn->close();
?>
