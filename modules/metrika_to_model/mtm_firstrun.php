<?php
require_once '../../settings/config.php';
require_once '../../settings/auth.php';
require_once '../../settings/gsheet-auth.php';

include 'mtm.php';

// Настройки запроса к Метрике
// $today = new DateTime('2020-03-31', new DateTimeZone('Europe/Moscow'));
$date1 = 		'2020-01-01';
$metrics= 	'ym:s:visits,ym:s:users,ym:s:pageDepth,ym:s:bounceRate';
$dimensions='ym:s:date,ym:s:startURLDomain';
$sort= 			'ym:s:date,-ym:s:visits';

// пустой массив для отправки строк в Gsheet
$strArr 	= [];

clearTable($service, $spreadsheetId);

	// Функция обновления строк отчета
	function addRows($date1,$group,$metrics,$dimensions,$sort,$service, $spreadsheetId) {
		$date = date_create($date1);
		print_r('Первая Дата в цикле: ');
		print_r(date_format($date, 'Y-m-d'));
		$date1 = date_format($date, 'Y-m-d');
		$date2 = $date2 = date_format(date_add($date, date_interval_create_from_date_string('7 days')), 'Y-m-d');
		print_r('<br><br>Первая Date1: ');
		print_r($date1);
		print_r('<br>Первая Date2: ');
		print_r($date2);
		$today = new DateTime();
		$yesterday = date_sub($today, date_interval_create_from_date_string('1 day'));
		print_r('<br>ДО: ');
		print_r(date_format($yesterday, 'Y-m-d H:i'));
		print_r('<br>');
		
		while ($date < $yesterday) {
			[$data, $query] = sendRequest($date1,$date2,$group,$metrics,$dimensions,$sort);
			foreach ($data as $val) {
				$dimdate = date('Y-m-d',strtotime($val->dimensions[0]->name));
				$domain = $val->dimensions[1]->name;
				$visits = (int) $val->metrics[0];
				$users = (int) $val->metrics[1];
				$depth = round($val->metrics[2], 2);
				$bounce = round($val->metrics[3], 2);

				$strArr[] = [
						$dimdate,
						$domain,
						$visits,
						$users,
						$depth,
						$bounce,
				];

		}

		$date1 = date_format(date_add(new DateTime($date2), date_interval_create_from_date_string('1 day')), 'Y-m-d');
		print_r('<br>Date1: ');
		print_r($date1);
		$date2 = date_format(date_add(new DateTime($date1), date_interval_create_from_date_string('7 days')), 'Y-m-d');
		print_r('<br>Date2: ');
		print_r($date2);
		$date = date_add($date, date_interval_create_from_date_string('8 days'));
		print_r('<br>Date: ');
		print_r($date2);
		print_r('<br><br>');
	}

	$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $strArr]);
	$options = ['valueInputOption' => 'RAW'];
	$list = "'Выгрузка всех визитов'!";
	$row = $list . "A2";
	$result = $service->spreadsheets_values->append($spreadsheetId, $row, $ValueRange, $options);
	
	updateHeaders($query, $service, $spreadsheetId);
	print_r('<br>Конец скрипта');
return $result;
}

addRows($date1,$group,$metrics,$dimensions,$sort,$service, $spreadsheetId);
