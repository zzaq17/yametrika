<?php
require_once 'settings/config.php';
require_once 'settings/auth.php';
require_once 'settings/gsheet-auth.php';


function updateGsheet() {

	global $counterIDs;
	global $headers;
	global $reports;
	global $goalTypes;
	global $goalCondsTypes;
	global $service;

	$spreadsheetId = '1dQgxqMHVwApxEp1ZqcODMlXgX3UmKzoBsh_RTuHt6Ss';


	$reportType = 'goals';
		// Массив, в который добавятся все строки с целями
		$fullArray = [];

		// Цикл перебора счетчиков Метрики из config.php
		foreach ($counterIDs as $key => $counterID) {

			$request = 'https://api-metrika.yandex.net/management/v1/counter/'. $counterID .'/'.$reports[$reportType].'';
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_VERBOSE, 1);
			curl_setopt($curl, CURLOPT_URL, $request);
			// curl_setopt($curl, CURLOPT_POST, true); // true - означает, что отправляется POST запрос
			
			$result = curl_exec($curl);
			$goals = json_decode($result)->goals;
			$currentDomain = $key;

			foreach ($goals as $key => $val) {
				$g = $goals[$key];
				if (property_exists($g, 'conditions')) {
					$c = $g->conditions[0];
				}
				
					switch ($g->type) {

						case 'url':
									$fullArray[] = [
										$currentDomain,
										$g->id,
										$g->name,
										$goalTypes[$g->type],
										$g->is_retargeting,
										$g->goal_source,
										$g->default_price,
										$goalCondsTypes[$c->type],
										$c->url,
									];
						break;

						case 'action':
									$fullArray[] = [
										$currentDomain,
										$g->id,
										$g->name,
										$goalTypes[$g->type],
										$g->is_retargeting,
										$g->goal_source,
										$g->default_price,
										$goalCondsTypes[$c->type],
										$c->url,
									];
									break;
									
									default:
									$fullArray[] = [
										$currentDomain,
										$g->id,
										$g->name,
										$goalTypes[$g->type],
										$g->is_retargeting,
										$g->goal_source,
										$g->default_price,
									];
							break;
						}
				}
		}

		// Отправка массива в Google Sheets
		$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $fullArray]);
		// Указываем в опциях обрабатывать пользовательские данные
		$options = ['valueInputOption' => 'RAW'];
		// Имя листа для вставки
		$list = 'Выгрузка всех визитов';
		// Ячейка, с которой начинаем вставку
		$row = $list . 'A2';
		
			// Делаем запрос с указанием во втором параметре названия листа и начальную ячейку для заполнения
			$result = $service->spreadsheets_values->update($spreadsheetId, $row, $ValueRange, $options);

	return $result;
}

updateGsheet();