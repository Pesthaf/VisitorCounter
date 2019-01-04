<?php

// Определяем константы
define('DB_FILENAME', 'counter.db');
define('DB_TABLENAME', 'visitors');
define('DEBUG', false);

// Подключаем БД
$db_path = dirname(__FILE__) . '/' . DB_FILENAME;
if (!file_exists($db_path)) {
	if (DEBUG) echo "Пытаемся создать БД" . PHP_EOL;
	try {
		$db = new SQLite3($db_path);
	} catch (Exception $e) {
		if (DEBUG) echo "Ошибка создания БД, проверьте права доступа в папке и наличие модуля PHP-SQLite3 (Исключение: " . $e->getMessage() . ")" . PHP_EOL;
		exit;
	}
	$request = 'CREATE TABLE `' . DB_TABLENAME . '` (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		url TEXT,
		ip TEXT,
		useragent TEXT,
		date INTEGER);';
	$result = $db->exec($request);
	if (!$result) {
		if (DEBUG) echo "Ошибка создания таблицы, прерываем работу скрипта" . PHP_EOL;
		exit;
	}
} else $db = new SQLite3($db_path);

// Получаем данные клиента
$url = $_SERVER['REQUEST_URI'];
$ip = getRealIpAddr();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$date = time();
if (DEBUG) echo "Данные о клиенте: $url, $ip, $useragent, $date" . PHP_EOL;

// Записываем в БД
$request = "INSERT INTO `" . DB_TABLENAME . "` (
	`url`, `ip`, `useragent`, `date`)
	VALUES ('$url', '$ip', '$useragent', '$date');";
$result = $db->exec($request);
if (DEBUG) if ($result) echo "Посетитель успешно занесен в БД.";
else echo "Ошибка выполнения запроса INSERT в БД.";

// Закрываем БД
$db->close();

// Возвращет реальный айпи в случае использования прокси
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

?>