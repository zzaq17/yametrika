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
