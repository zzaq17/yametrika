<?php 
	require_once 'settings/auth.php';
	require_once 'settings/config.php';
	require_once 'classes/classes.php';
	require_once 'settings/gsheet-auth.php';

	
	// $domain = 'laparoskopiya.ru';
	// $counterID = $counterIDs[$domain];
	$reportType = 'goals';

// //Give our CSV file a name.
$csvFileName = 'goals_RPM_'.$today.'.csv';

//Open file pointer.
$buffer = fopen(__DIR__.'\\responses\\'.$csvFileName, 'w');
fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));

$fullArray = [];

$csvheaders = [
	'Сайт',
	'id цели',
	'Название',
	'Тип цели',
	'Ретаргетинг',
	'Способ создания',
	'Ценность цели',
	'Условие цели',
	'URL',
];
fputcsv($buffer, $csvheaders);

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

	$csvgoals = [];
	foreach ($goals as $key => $val) {
		$g = $goals[$key];
		if (property_exists($g, 'conditions')) {
			$c = $g->conditions[0];
		}
		
			switch ($g->type) {

				case 'url':
							$csvgoals = [
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
							// print_r($key . ': URL '); print_r($csvgoals);
							fputcsv($buffer, $csvgoals);
							array_push($fullArray, $csvgoals);
				break;

				case 'action':
							$csvgoals = [
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
							// print_r($key . ': ACTION '); print_r($csvgoals);
							fputcsv($buffer, $csvgoals);
							array_push($fullArray, $csvgoals);
							break;
							
							default:
							$csvgoals = [
								$currentDomain,
								$g->id,
								$g->name,
								$goalTypes[$g->type],
								$g->is_retargeting,
								$g->goal_source,
								$g->default_price,
							];
							// print_r($key . ': DEFAULT '); print_r($csvgoals);
							fputcsv($buffer, $csvgoals);
							array_push($fullArray, $csvgoals);
					break;
				}
			print_r('<br>');
		}
}

fclose($buffer);
// print_r($fullArray);
// Объект - диапазон значений
$ValueRange = new Google_Service_Sheets_ValueRange(['values' => $fullArray]);
// Устанавливаем наши данные
// Указываем в опциях обрабатывать пользовательские данные
$options = ['valueInputOption' => 'RAW'];
$list = 'Выгрузка всех целей!';
$addingRange = $list . 'A2:I'.count($fullArray);


// Делаем запрос с указанием во втором параметре названия листа и начальную ячейку для заполнения
	$result = $service->spreadsheets_values->update($spreadsheetId, $addingRange, $ValueRange, $options);


exit();
