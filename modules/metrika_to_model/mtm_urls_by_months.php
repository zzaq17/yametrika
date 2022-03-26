<?php
require_once '../../settings/config.php';
require_once '../../settings/auth.php';
require_once '../../settings/gsheet-auth.php';

include 'mtm.php';

// Настройки запроса к Метрике
$firstDate1 = 		'2021-01-01';
$metrics= 	'ym:s:visits,ym:s:users,ym:s:pageDepth,ym:s:bounceRate';
$dimensions='ym:s:datePeriod<group>,ym:s:startURLDomain,ym:s:startURLPath';
$sort= 			'ym:s:datePeriod<group>,ym:s:startURLDomain,-ym:s:visits';
$group= 		'month';

// Лист GSheet для работы
$list = "'urls_by_months_API'!";


// clearTable($service, $spreadsheetId, $list);
// Функция обновления строк отчета
function addMonths($firstDate1,$date2,$counterIDs,$group,$metrics,$dimensions,$sort,$service,$spreadsheetId,$list) {
		// очистка листа
		clearTable($service, $spreadsheetId, $list);

		
		// Перебор id счетчиков метрики из ассоциированного массива $ids
		foreach ($counterIDs as $project => $counter) {
			// Подготовка стартовых дат
				// установка текущего месяца
			$stopDate = date_format(new DateTime('first day of next month 00:00', new DateTimeZone('Europe/Moscow')), 'Y-m-d');

				// установка дат для диапазона отчета Метрики
			$date1 = date_format(new DateTime($firstDate1, new DateTimeZone('Europe/Moscow')), 'Y-m-d');
			$date2 = date_format(date_add(new DateTime($firstDate1, new DateTimeZone('Europe/Moscow')), date_interval_create_from_date_string('1 month')), 'Y-m-t');
			print_r('<br><br>        Стартовая дата:  '.$date1);
			print_r('<br>        Стартовый период до: '.$date2);
			print_r('<br>        Конечная дата:       '.$stopDate);


			// пустой массив для отправки строк в Gsheet
			$strArr 	= [];
			// print_r('<br><br>counter: '.$counter);
			// print_r('<br>project: '.$project);
			// Перебор всех месяцев с даты старта $date1 до конца сегоднящнего месяца
			while ($date1 <= $stopDate) {
				
				// print_r('<br><br>        date1: '.$date1);
				// print_r('<br>        date2: '.$date2);
				// Отправка запроса в Метрику. Получение двух объектов: Данные и Заголовки
				[$data, $query] = sendRequest($date1,$date2,$counter,$group,$metrics,$dimensions,$sort);
				// Перебор данных из data и добавление их в строки gsheet
				foreach ($data as $val) {
					if (empty($val->dimensions[1]->name)) {
						// nothing to do if "domain" is empty
					}
					else {
						$month = date('Y-m-d',strtotime($val->dimensions[0]->id));
						$domain = $val->dimensions[1]->name;
						$url = $val->dimensions[2]->name;
						// Очистка url от параметров и якорных ссылок
						$del_symb = ['?','#'];
						foreach ($del_symb as $sym) {
							$pos = strpos($url, $sym);
							if ($pos === false) {
								// nothing to do if there is no any of the symbols
							} else {
								// print_r('<br><br>'.$url);
								list($url_part, $qs_part) = array_pad(explode($sym, $url), 2, "");
								$url = $url_part;
								// print_r('<br><br>'.$url);
								// print_r('<br>отброшено:'.$qs_part);
							}
						}
						$visits = (int) $val->metrics[0];
						$users = (int) $val->metrics[1];
						$depth = round($val->metrics[2], 2);
						$bounce = round($val->metrics[3], 2);
						// формирование массива (строки gsheet) для отправки в ghsheet
						$strArr[] = [
								$month,
								$domain,
								$url,
								$visits,
								$users,
								$depth,
								$bounce,
								$domain,
							];
						}
					}
				// шаг дат на 1 месяц вперед для продолжения цикла
				$date1 = date_format(date_add(new DateTime($date2), date_interval_create_from_date_string('1 day')), 'Y-m-d');
				$date2 = date_format(date_add(new DateTime($date1), date_interval_create_from_date_string('1 month')), 'Y-m-t');
					
			}
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

		return $result;
		print_r('<br><br>Скрипт отрабтал!');
		// return $strArr;
	}

addMonths($firstDate1,$date2,$counterIDs,$group,$metrics,$dimensions,$sort,$service,$spreadsheetId,$list);