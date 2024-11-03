<?php
function loadEnv($path)
{
  if (!file_exists($path)) {
    throw new Exception(".env file not found");
  }

  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) {
      continue;
    }

    list($name, $value) = explode('=', $line, 2);
    $name = trim($name);
    $value = trim($value);

    // Удаляем возможные кавычки вокруг значения
    $value = trim($value, "'\"");

    $_ENV[$name] = $value;
    $_SERVER[$name] = $value;
  }
}
loadEnv(__DIR__ . '/.env');