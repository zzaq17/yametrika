<?php
require_once '../../settings/config.php';
require_once '../../settings/auth.php';
require_once '../../settings/gsheet-auth.php';

function sfss_to_gs($filename='', $delimiter=';') {

	global $service;
	global $spreadsheetId;
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;
	
	$data = [];
	$csv_rows = [];
	if (($handle = fopen($filename, 'r')) !== FALSE) {
		fgetcsv($handle, 4000, $delimiter);
		while (($row = fgetcsv($handle, 4000, $delimiter)) !== FALSE) {
			$i = 1;
			$j = 2;
			foreach ($row as $key) {
					if (strlen($row[$i]) > 0) {
					$url = $row[0];
					$data[] = [
						[$url, $row[$i], $row[$j]],
					];
					$i = $i+2;
					$j = $j+2;
				}
			}
		}
		fclose($handle);
	}

		foreach ($data as $row) {
				$r = $row[0];

				$csv_rows[] = [
					$r[0], 
					$r[1], 
					$r[2] 
				];
			}
			// print_r($csv_rows);
			// fclose($handle);

	// Отправка массива в Google Sheets
	$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $csv_rows]);
	// Указываем в опциях обрабатывать пользовательские данные
	$options = ['valueInputOption' => 'RAW'];
	// Имя листа для вставки
	$list = 'Карты форм!';
	// Ячейка, с которой начинаем вставку
	$row = $list . 'B2';
	$result = $service->spreadsheets_values->update($spreadsheetId, $row, $ValueRange, $options);
	
	return $result;
}


function sfss_clear_gs(){

	global $service;
	global $spreadsheetId;

	$list = 'Карты форм!';
	// Ячейка, с которой начинаем вставку
	$row = $list . 'B2';
$rowClear = $row . ':D';
// Объект - запрос очистки значений
$clear = new Google_Service_Sheets_ClearValuesRequest();
// Делаем запрос с указанием во втором параметре названия листа и диапазон ячеек для очистки
$response = $service->spreadsheets_values->clear($spreadsheetId, $rowClear, $clear);
	// Делаем запрос с указанием во втором параметре названия листа и начальную ячейку для заполнения
return $response;
}

sfss_clear_gs();
sfss_to_gs("sfss/sfss.csv");
