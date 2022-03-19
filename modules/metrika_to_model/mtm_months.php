<?php
require_once '../../settings/config.php';
require_once '../../settings/auth.php';
require_once '../../settings/gsheet-auth.php';

include 'mtm.php';

// Настройки запроса к Метрике
$date1 = 		'2020-01-01';
$metrics= 	'ym:s:visits,ym:s:users,ym:s:pageDepth,ym:s:bounceRate';
$dimensions='ym:s:datePeriod<group>,ym:s:startURLDomain';
$sort= 			'ym:s:datePeriod<group>,-ym:s:visits';
$group= 		'month';

// Лист GSheet для работы
$list = "'months_metrika_API'!";


// clearTable($service, $spreadsheetId, $list);
// Функция обновления строк отчета
function addMonths($date1,$date2,$ids,$group,$metrics,$dimensions,$sort,$service,$spreadsheetId,$list) {
		// очистка листа
		clearTable($service, $spreadsheetId, $list);

		// Подготовка стартовых дат
			// установка текущего месяца
		$stopDate = date_format(new DateTime('first day of next month 00:00', new DateTimeZone('Europe/Moscow')), 'Y-m-d');
			// установка дат для диапазона отчета Метрики
		$date1 = date_format(new DateTime($date1, new DateTimeZone('Europe/Moscow')), 'Y-m-d');
		$date2 = date_format(date_add(new DateTime($date1, new DateTimeZone('Europe/Moscow')), date_interval_create_from_date_string('3 month')), 'Y-m-t');

		// пустой массив для отправки строк в Gsheet
		$strArr 	= [];

		while ($date1 <= $stopDate) {
			// Отправка запроса в Метрику. Получение двух объектов: Данные и Заголовки
			[$data, $query] = sendRequest($date1,$date2,$ids,$group,$metrics,$dimensions,$sort);
			print_r('<br><br>Date1: ');
			print_r($date1);
			print_r('<br>Date2: ');
			print_r($date2);
			print_r('<br>stopDate: ');
			print_r($stopDate);
			foreach ($data as $val) {
				if (empty($val->dimensions[1]->name)) {
					// nothing to do if "domain" is empty
				}
				else {
					$month = date('Y-m-d',strtotime($val->dimensions[0]->id));
					$domain = $val->dimensions[1]->name;
					$visits = (int) $val->metrics[0];
					$users = (int) $val->metrics[1];
					$depth = round($val->metrics[2], 2);
					$bounce = round($val->metrics[3], 2);
					// print_r('<br>');
					// print_r('<br>');
					// print_r($month);
					// print_r('<br>');
					// print_r($domain);
					// print_r('<br>');
					// print_r($visits);
					// print_r('<br>');
					// print_r($users);
					// print_r('<br>');
					// print_r($depth);
					// print_r('<br>');
					// print_r($bounce);
					// print_r('<br>');
					// print_r('<br>');
					
					$strArr[] = [
						$month,
						$domain,
						$visits,
						$users,
						$depth,
							$bounce,
						];
					}
				}
			$date1 = date_format(date_add(new DateTime($date2), date_interval_create_from_date_string('1 day')), 'Y-m-d');
			$date2 = date_format(date_add(new DateTime($date1), date_interval_create_from_date_string('3 month')), 'Y-m-t');
				
			// подготовка диапазона к отправке
			$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $strArr]);
			$options = ['valueInputOption' => 'RAW'];
			// диапазон
			$row = $list . "A2";
			// Отправка в google sheets
			$result = $service->spreadsheets_values->append($spreadsheetId, $row, $ValueRange, $options);
				
		}
			// Добавление заголовков в первую строку
		updateHeaders($query,$service,$spreadsheetId,$list);

		// print_r('<br><br>Скрипт отрабтал');
		return $result;
		// return $strArr;
	}

addMonths($date1,$date2,$ids,$group,$metrics,$dimensions,$sort,$service,$spreadsheetId,$list);