<?php
require_once 'config.php';
// Подключаем клиент Google таблиц
require_once __DIR__ . '/../vendor/autoload.php';

// Наш ключ доступа к сервисному аккаунту
$googleAccountKeyFilePath = __DIR__ . '/service_key.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);

// Создаем новый клиент
$client = new Google_Client();
// Устанавливаем полномочия
$client->useApplicationDefaultCredentials();

// Добавляем область доступа к чтению, редактированию, созданию и удалению таблиц
$client->addScope('https://www.googleapis.com/auth/spreadsheets');

$service = new Google_Service_Sheets($client);

// ID таблицы
$spreadsheetId = $gsheetID;