<?php 
	require_once 'settings/auth.php';
	require_once 'settings/config.php';
	require_once 'classes/classes.php';
	require_once 'settings/gsheet-auth.php';



// function updateGoals() {
// global $today;
// global $counterIDs;
// global $headers;
// global $goalTypes;
// global $goalCondsTypes;

$reportType = 'goals';
//Give our CSV file a name
$csvFileName = 'goals_RPM_'.$today.'.csv';
//Open file pointer
$buffer = fopen(__DIR__.'\\responses\\'.$csvFileName, 'w');
fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));


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

	// Цикл перебора счетчиков Метрики из config.php
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
								fputcsv($buffer, $csvgoals);
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
								fputcsv($buffer, $csvgoals);
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
								fputcsv($buffer, $csvgoals);
						break;
					}
				print_r('<br>');
			}
	}

	fclose($buffer);

	exit();
// }

// updateGoals();
