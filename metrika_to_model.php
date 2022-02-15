<?php
require_once 'settings/config.php';
require_once 'settings/auth.php';
require_once 'settings/gsheet-auth.php';

// функция добавляет в таблицу данные
function appendVisits() {

	global $headers;
	global $service;

	$spreadsheetId = '1dQgxqMHVwApxEp1ZqcODMlXgX3UmKzoBsh_RTuHt6Ss';

	// Настройки запроса к Метрике
	$date1 = '2020-01-01';
	$date2 = '2020-12-31';
	$today = date('yyyy-mm-dd');
	
	$fullArray = [];

			$request = "https://api-metrika.yandex.net/stat/v1/data/bytime?metrics=ym:s:visits,ym:s:users&ids=50212432,48002045,50337838,56942572,57655366,62131978,72684979,46733565,49221070,46696410,49052279,51933194&accuracy=1&date1=".$date1."&date2=".$date2."&dimensions=ym:s:startURLDomain&filters=ym:s:isRobot=='No'&group=month&include_annotations=false&proposed_accuracy=true&sort=-ym:s:users";
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_VERBOSE, 1);
			curl_setopt($curl, CURLOPT_URL, $request);
			// curl_setopt($curl, CURLOPT_POST, true); // true - означает, что отправляется POST запрос
			
			$result = curl_exec($curl);
			$resultDecode = json_decode($result);
			$data = $resultDecode->data;
			$timeIntervals = $resultDecode->time_intervals;

			var_dump($data);
			var_dump($timeIntervals);

			// $currentDomain = $key;

			// foreach ($goals as $key => $val) {
			// 	$g = $goals[$key];
			// 	if (property_exists($g, 'conditions')) {
			// 		$c = $g->conditions[0];
			// 	}
				
			// 		switch ($g->type) {

			// 			case 'url':
			// 						$fullArray[] = [
			// 							$currentDomain,
			// 							$g->id,
			// 							$g->name,
			// 							$goalTypes[$g->type],
			// 							$g->is_retargeting,
			// 							$g->goal_source,
			// 							$g->default_price,
			// 							$goalCondsTypes[$c->type],
			// 							$c->url,
			// 						];
			// 			break;

			// 			case 'action':
			// 						$fullArray[] = [
			// 							$currentDomain,
			// 							$g->id,
			// 							$g->name,
			// 							$goalTypes[$g->type],
			// 							$g->is_retargeting,
			// 							$g->goal_source,
			// 							$g->default_price,
			// 							$goalCondsTypes[$c->type],
			// 							$c->url,
			// 						];
			// 						break;
									
			// 						default:
			// 						$fullArray[] = [
			// 							$currentDomain,
			// 							$g->id,
			// 							$g->name,
			// 							$goalTypes[$g->type],
			// 							$g->is_retargeting,
			// 							$g->goal_source,
			// 							$g->default_price,
			// 						];
			// 				break;
			// 			}
			// 	}

		// Отправка массива в Google Sheets
		$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $fullArray]);
		// Указываем в опциях обрабатывать пользовательские данные
		$options = ['valueInputOption' => 'RAW'];
		// Имя листа для вставки
		$list = "'Выгрузка всех визитов'!";
		// Ячейка, с которой начинаем вставку
		$row = $list . "A2";
		
			// Делаем запрос с указанием во втором параметре названия листа и начальную ячейку для заполнения
			$result = $service->spreadsheets_values->update($spreadsheetId, $row, $ValueRange, $options);

	return $result;
}

appendVisits();