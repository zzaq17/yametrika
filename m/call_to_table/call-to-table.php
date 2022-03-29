<?php
require_once '../../settings/config.php';
require_once '../../settings/gsheet-auth.php';
// require_once 'bot/rpm-leads-bot.php';
		// Отладка
		// $json = '{"id":"19808","caller":"79601144551","callee":"74999554025","visit_id":null,"marker":"registraciyaznaka.ru","order_id":null,"date":"2022-01-26 19:01:04"}';
		// $data = json_decode(trim($json), true);
	// Получаем данные звонка
		$data = json_decode(trim(file_get_contents('php://input')), true);
			// логируем на сервер
		$nowdate = date("d.m.Y H:i");
		$normCaller = trim($data['caller']);
		if ($normCaller[0] == 7) {
			$normCaller = "+" . $normCaller[0] . ' ('.$normCaller[1].$normCaller[2].$normCaller[3].') '.$normCaller[4].$normCaller[5].$normCaller[6].'-'.$normCaller[7].$normCaller[8].'-'.$normCaller[9].$normCaller[10].'';
		}

			file_put_contents('webhook-log.txt', "id {$data['id']} {$nowdate}: {$normCaller} -> {$data['callee']}\n", FILE_APPEND); 

	// Отправляем в Gsheet
			$forsending = [['Новая','',$normCaller,$nowdate,'','',$data['id'],'zvonok','https://' . $data['marker']]];
				print_r($forsending);
			$crm_id = '1hzXUjuj9xpqhlnC1G-NcJ6vAxT7Jln3os5tBbjay5k0';
			$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $forsending]);
			$options = ['valueInputOption' => 'RAW'];
			$range = 'LeadsFromTilda!A:G';

			$result = $service->spreadsheets_values->append($crm_id, $range, $ValueRange, $options);

	// Отправляем на email
			// $recepient = "partner@rpmgroup.ru";
			// $sitename = "RPM Group";
			// $dateTime = date("d.m.y H:i");
			// $to  = 'a.zakharov@rpmgroup.ru' . ', '; // кому отправляем
			// $to .= '31@brand-patent.ru' . ', '; // Внимание! Так пишем второй и тд адреса
			// // переменные письма
			// $bgc = "#3480e3";
			// // содержание письма
			// $subject = 'Заявка от RPM';
			// $message = '
			// <head>
			// 	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			// 	<title>'.$subject.'</title>
			// </head>
			// <body>
			// 	<table style="width: 100%; max-width: 600px; border-collapse: collapse;">
			// 		<tr style="background-color: '.$bgc.'; background-image: url(../img/Logo-Klever-1x1.png)">
			// 			<td style="padding: 20px; text-align: center; color: white; font-size: 22px" colspan="2"><b>Заявка от RPM</b></td>
			// 		</tr>
			// 		<tr>
			// 			<td style="padding: 10px; border: #eee 1px solid; max-width: 140px;"><b>Телефон клиента</b></td>
			// 			<td style="padding: 10px; border: #eee 1px solid;">'.$data['caller'].'</td>
			// 		</tr>
			// 		<tr style="background-color: #eee">
			// 			<td style="padding: 10px; border: #eee 1px solid; max-width: 140px;"><b>Дата</b></td>
			// 			<td style="padding: 10px; border: #eee 1px solid;">'.$data['date'].'</td>
			// 		</tr>
			// 		</table>
			// </body>
			// 		';
			// // устанавливаем тип сообщения Content-type, если хотим
			// $headers  = "MIME-Version: 1.0" . PHP_EOL .
			// "Content-Type: text/html; charset=utf-8" . PHP_EOL .
			// "From: ".$sitename." <".$recepient.">" . PHP_EOL .
			// 'Reply-To: '.$recepient.'' . PHP_EOL;
			// // отправляем email
			// mail($to, $subject, $message, $headers);
		
	// 	if($data){
		// // Отправляем в Telegram
		// 		$leadText = '';
		// 		$arr = array(
		// 			'Телефон: ' => $data['caller'],
		// 			'Дата заявки: ' => $data['date'],
		// 			'Страница: ' => $data['marker'],
		// 		);

		// 		$leadText = "<b>Заявка от RPM</b>%0A";
		// 		foreach($arr as $key => $value) {
		// 			$txt .= "<b>".$key."</b> ".$value."%0A";
		// 		};
		// 		sendLead($chat_id, $leadText);

		// 	}
		// 	else {
		// 		print("It's nothing ro send");
		// 	}

			

		
