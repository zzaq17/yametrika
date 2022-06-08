<?php
require_once '../../settings/config.php';
require_once '../../settings/auth.php';
require_once '../../settings/gsheet-auth.php';

// функция добавляет в таблицу данные
$spreadsheetId = '1dQgxqMHVwApxEp1ZqcODMlXgX3UmKzoBsh_RTuHt6Ss';

// Настройки запроса к Метрике
$date1 = 		'2021-01-01';
$date2 = 		'2021-01-20';
$group = 		'month';
$metrics= 	'ym:s:visits,ym:s:users,ym:s:pageDepth,ym:s:bounceRate';
$dimensions='ym:s:date,ym:s:startURLDomain';
$sort =			'wsk|desc';
$readySort= urlencode($sort);
$phrase = '';

print_r($sort . ' = ' . $readySort . '<br><br>');

// Функция получения json-ответа из Метрики по API-запросу. На выходе два массива: query - массив с заголовками, data - массив с метриками.
	function sendRequest($sort) {
			global $keysHeaders;

			$request = "https://api.keys.so/report/simple/organic/sitepages?base=msk&domain=laparoskopiya.ru&page=1&per_page=50000";
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER, $keysHeaders);
			curl_setopt($curl, CURLOPT_URL, $request);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
			curl_setopt($curl, CURLOPT_TIMEOUT, 15);
			$response = curl_exec($curl);
			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$headerCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			// print_r('<br>'.$headerCode);
			$responseBody = substr($response, $header_size);
			// print_r('<br><br>'.$responseBody.'<br><br>');
			curl_close($curl);


			$resultDecode = json_decode($responseBody);
			// $resultDecode = $responseBody;
			// print_r($resultDecode);
			// // Обработка ошибок
			// if (empty($resultDecode->errors)) {
			// 	$data = $resultDecode->data;
			// 	$query = $resultDecode->query;
			return $resultDecode;
			// }
			// else {
			// 	// print_r('<br><br>' .'empty data for period: '. $date1 . ' - ' . $date2);
			// 	// print_r('<br><br>' .'<b>error-type:</b> '. $resultDecode->errors[0]->error_type . '<br> <b>message:</b> ' . $resultDecode->errors[0]->message);
			// }
	}
	
	sendRequest($sort);
