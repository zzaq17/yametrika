<?php
require_once '../../settings/config.php';
require_once '../../settings/auth.php';
require_once '../../settings/gsheet-auth.php';

// функция добавляет в таблицу данные
$spreadsheetId = '1dQgxqMHVwApxEp1ZqcODMlXgX3UmKzoBsh_RTuHt6Ss';

// Настройки запроса к Метрике
$date1 = 		'2020-01-01';
$date2 = 		'2020-01-20';
$group = 		'month';
$metrics= 	'ym:s:visits,ym:s:users,ym:s:pageDepth,ym:s:bounceRate';
$dimensions='ym:s:date,ym:s:startURLDomain';
$sort= 			'ym:s:date,-ym:s:visits';

// Функция получения json-ответа из Метрики по API-запросу. На выходе два массива: query - массив с заголовками, data - массив с метриками.
	function sendRequest($date1,$date2,$ids,$group,$metrics,$dimensions,$sort) {
			global $headers;
			$request = "https://api-metrika.yandex.net/stat/v1/data/?metrics=".$metrics."&ids=".$ids."&accuracy=1&date1=".$date1."&date2=".$date2."&group=".$group."&dimensions=".$dimensions."&filters=ym:s:isRobot=='No'&include_annotations=false&proposed_accuracy=true&sort=".$sort."";
			// print($request);
			// print_r('<br>');
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_TIMEOUT, 300);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_VERBOSE, 1);
			curl_setopt($curl, CURLOPT_URL, $request);
			// curl_setopt($curl, CURLOPT_POST, true); // true - означает, что отправляется POST запрос
			
			$result = curl_exec($curl);
			$resultDecode = json_decode($result);

			$data = $resultDecode->data;
			$query = $resultDecode->query;
		return [$data, $query];
	}
	
	// [$data, $query] = sendRequest($date1,$date2,$group,$metrics,$dimensions,$sort);


// очистка всех заголовков в первой строке таблицы
	function clearHeaders($service, $spreadsheetId) {
			$list = "'visits_metrika_API'!";
			$row = $list . "1:1";
			$clear = new Google_Service_Sheets_ClearValuesRequest();
			$result = $service->spreadsheets_values->clear($spreadsheetId, $row, $clear);
		
		return $result;
	}
	
	// clearHeaders($service, $spreadsheetId);
	

// очистка таблицы
	function clearTable($service, $spreadsheetId, $list) {
		// $list = "'visits_metrika_API'!";
		$row = $list . "A:Z";
		$clear = new Google_Service_Sheets_ClearValuesRequest();
		$result = $service->spreadsheets_values->clear($spreadsheetId, $row, $clear);
		
		return $result;
	}
	
	// clearTable($service, $spreadsheetId);


// обновить Заголовки в таблице Заголовками из query ответа
	function updateHeaders($query, $service, $spreadsheetId, $list) {
			$dimHeaders = $query->dimensions;
			$metHeaders = $query->metrics;
			$headersArr = [
				array_merge($dimHeaders, $metHeaders)
			];

			$options = ['valueInputOption' => 'RAW'];
			$row = $list . "A1";
			$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $headersArr]);
			$result = $service->spreadsheets_values->update($spreadsheetId, $row, $ValueRange, $options);

		return $result;
	}

	// updateHeaders($query, $service, $spreadsheetId);


// Функция замены строк отчета
	function updateRows($data, $service, $spreadsheetId,$list) {
		foreach ($data as $val) {
				$date = date('d.m.y',strtotime($val->dimensions[0]->name));
				$domain = $val->dimensions[1]->name;
				// $url = $val->dimensions[2]->name;
				$visits = (int) $val->metrics[0];
				$users = (int) $val->metrics[1];
				$depth = round($val->metrics[2], 2);
				$bounce = round($val->metrics[3], 2);

				$strArr[] = [
						$date,
						$domain,
						// $url,
						$visits,
						$users,
						$depth,
						$bounce,
				];
			}

			$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $strArr]);
			$options = ['valueInputOption' => 'RAW'];
			$row = $list . "A2";
			$result = $service->spreadsheets_values->update($spreadsheetId, $row, $ValueRange, $options);

		return $result;
	}

// updateRows($data, $service, $spreadsheetId);


// Функция обновления строк отчета
function appendRows($data, $service, $spreadsheetId,$list) {
	foreach ($data as $val) {
		$date = date('d.m.Y', strtotime($val->dimensions[0]->name));
		$domain = $val->dimensions[1]->name;
		$visits = (int) $val->metrics[0];
		$users = (int) $val->metrics[1];
		$depth = round($val->metrics[2], 2);
		$bounce = round($val->metrics[3], 2);

		$strArr[] = [
				$date,
				$domain,
				$visits,
				$users,
				$depth,
				$bounce,
		];
	}

	$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $strArr]);
	$options = ['valueInputOption' => 'RAW'];
	$row = $list . "A2";
	$result = $service->spreadsheets_values->append($spreadsheetId, $row, $ValueRange, $options);

return $result;
}

// appendRows($data, $service, $spreadsheetId);