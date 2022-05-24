<?php
require_once '../../settings/config.php';
require_once '../../settings/gsheet-auth.php';
	// Получаем данные звонка
		$d = json_decode(trim(file_get_contents('php://input')), true);
			// логируем на сервер
			file_put_contents('wh-antiageklinika-log.txt', "id {$d['id']} {$d['date']}: {$d['status']} {$d['caller']} -> {$d['callee']}. from {$d['landing_page']}\n", FILE_APPEND); 

	// Отправляем в Gsheet
			$forsending = [
				[
					$d['id'],
					$d['caller'],
					$d['callee'],
					$d['visit_id'],
					$d['marker'],
					$d['order_id'],
					$d['date'],
					$d['google_client_id'],
					$d['custom_fields'],
					$d['landing_page'],
					$d['domain'],
					$d['city'],
					$d['country'],
					$d['ip'],
					$d['first_visit'],
					$d['referrer'],
					$d['utm_source'],
					$d['utm_medium'],
					$d['utm_campaign'],
					$d['utm_term'],
					$d['utm_content'],
					$d['roistat_param_1'],
					$d['roistat_param_2'],
					$d['roistat_param_3'],
					$d['roistat_param_4'],
					$d['roistat_param_5'],
					$d['metrika_client_id'],
					$d['source_level_1'],
					$d['source_level_2'],
					$d['status'],
					$d['file_id'],
					$d['duration'],
					$d['link'],
					]
			];
				print_r($forsending);
			$crm_id = '1RmzAkOLyD6nUlwoPxFlPQeBmitrNZwl3E2-aNzBVzkQ';
			$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $forsending]);
			$options = ['valueInputOption' => 'RAW'];
			$range = 'calls!A2';

			$result = $service->spreadsheets_values->append($crm_id, $range, $ValueRange, $options);


			/*
			Response: 
			Array (
[0] => Array (
	[0] => 20488
	[1] => 79601144551
	[2] => 78127706999
	[3] => 4123819
	[4] => organic
	[5] => 30965689
	[6] => 2022-04-30 20:10:24
	[7] => 111111111.1111111111
	[8] => Array
			(
					[480895] => antiageklinika.ru
					[480839] => Москва
					[520409] => Неизвестно
					[520403] => {metrikaClientld}
					[tags] => antiageklinika.ru
					[567275] => {landing Page}
					[568373] => Звонок
			)

	[9] => antiageklinika.ru
	[10] => antiageklinika.ru
	[11] => Москва
	[12] => Россия
	[13] => 89.113.127.166
	[14] => 4123819
	[15] => 
	[16] => 
	[17] => 
	[18] => 
	[19] => 
	[20] => 
	[21] => 
	,)
	)
			Request body: {"city":"Москва","country":"Россия","ip":"89.113.127.166","visit_id":"4123819","first_visit":"4123819","referrer":"","domain":"antiageklinika.ru","landing_page":"antiageklinika.ru","marker":"organic","utm_source":null,"utm_medium":null,"utm_campaign":null,"utm_term":null,"utm_content":null,"roistat_param_1":null,"roistat_param_2":null,"roistat_param_3":null,"roistat_param_4":null,"roistat_param_5":null,"google_client_id":"111111111.1111111111","metrika_client_id":null,"id":"20488","caller":"79601144551","callee":"78127706999","order_id":"30965689","date":"2022-04-30 20:10:24","custom_fields":{"480895":"antiageklinika.ru","480839":"Москва","520409":"Неизвестно","520403":"{metrikaClientld}","tags":"antiageklinika.ru","567275":"{landing Page}","568373":"Звонок"},"status":"NOANSWER","file_id":null,"duration":30,"link":"https:\/\/cloud.roistat.com\/projects\/63284\/calltracking\/call\/20488\/file\/42201c4482385e18b8d92bbac0f49a86"}
		*/