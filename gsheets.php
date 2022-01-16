<?php
require_once __DIR__.'/settings/gsheet-auth.php';


// function sheets($spreadsheetID, $client) {
// 	$sheets = array();    
// 	// Load Google API library and set up client
// 	$sheetService = new Google_Service_Sheets($client);
// 	$spreadSheet = $sheetService->spreadsheets->get($spreadsheetID);
// 	$sheets = $spreadSheet->getSheets();
// 	foreach($sheets as $sheet) {
// 			$sheets[] = $sheet->properties->sheetId;
// 	}   
// 	return $sheets;
// }

// $sheets = sheets($gsheetID, $client);
// foreach ($sheets as $key => $value) {
// 	$res = $sheets[$key]->properties->title;
// 	print_r($res);
// 	print_r('<br><br>');
// }

// // Получение содержимого всего листа по его имени
// $range = 'Выгрузка всех целей';
// $response = $service->spreadsheets_values->get($spreadsheetId, $range);
// var_dump($response);

							// // Объект - диапазон значений
							// $ValueRange = new Google_Service_Sheets_ValueRange();
							// // Устанавливаем наши данные
							// $ValueRange->setValues($csvgoals);
							// $row = 'A' . $gsheetRow;
							// // Указываем в опциях обрабатывать пользовательские данные
							// $options = ['valueInputOption' => 'USER_ENTERED'];
							// // Делаем запрос с указанием во втором параметре названия листа и начальную ячейку для заполнения
							// $service->spreadsheets_values->update($spreadsheetId, $row, $ValueRange, $options);