<?php
require_once 'settings/config.php';
require_once 'settings/gsheet-auth.php';
require_once 'bot/rpm-leads-bot.php';

	// Получаем данные
		$data = json_decode(trim(file_get_contents('php://input')), true);
		if($input_array){
			// логируем на сервер
			file_put_contents('webhook-log.txt', "id {$data['id']} {$data['date']}: {$data['caller']} -> {$data['callee']}\n", FILE_APPEND); 

	// Отправляем в Gsheet
			$forsending = [['Новая',0,'',$data['caller'],$data['id'],$data['date'],$data['marker']]];
			$crm_id = '1hzXUjuj9xpqhlnC1G-NcJ6vAxT7Jln3os5tBbjay5k0';
			$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $forsending]);
			$options = ['valueInputOption' => 'RAW'];
			$range = 'LeadsFromTilda!A:G';

			$result = $service->spreadsheets_values->append($crm_id, $range, $ValueRange, $options);

	// Отправляем на email
			$recepient = "partner@rpmgroup.ru";
			$sitename = "RPM Group";
			$dateTime = date("d.m.y H:i");
			$to  = 'a.zakharov@rpmgroup.ru' . ', '; // кому отправляем
			$to .= '31@brand-patent.ru' . ', '; // Внимание! Так пишем второй и тд адреса
			// переменные письма
			$bgc = "#3480e3";
			// содержание письма
			$subject = 'Заявка от RPM';
			$message = '
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>'.$subject.'</title>
			</head>
			<body>
				<table style="width: 100%; max-width: 600px; border-collapse: collapse;">
					<tr style="background-color: '.$bgc.'; background-image: url(../img/Logo-Klever-1x1.png)">
						<td style="padding: 20px; text-align: center; color: white; font-size: 22px" colspan="2"><b>Заявка от RPM</b></td>
					</tr>
					<tr>
						<td style="padding: 10px; border: #eee 1px solid; max-width: 140px;"><b>Телефон клиента</b></td>
						<td style="padding: 10px; border: #eee 1px solid;">'.$data['caller'].'</td>
					</tr>
					<tr style="background-color: #eee">
						<td style="padding: 10px; border: #eee 1px solid; max-width: 140px;"><b>Дата</b></td>
						<td style="padding: 10px; border: #eee 1px solid;">'.$data['date'].'</td>
					</tr>
					</table>
			</body>
					';
			// устанавливаем тип сообщения Content-type, если хотим
			$headers  = "MIME-Version: 1.0" . PHP_EOL .
			"Content-Type: text/html; charset=utf-8" . PHP_EOL .
			"From: ".$sitename." <".$recepient.">" . PHP_EOL .
			'Reply-To: '.$recepient.'' . PHP_EOL;
			// отправляем email
			mail($to, $subject, $message, $headers);
			// Отправляем в Telegram
			// lead_to_tg($tg_token, $data);
		}
		else {
			print("It's nothing ro send");
		}