<?php
require_once '../../settings/config.php';
require_once '../../settings/auth.php';
require_once '../../settings/gsheet-auth.php';

include 'mtm.php';


// функция добавляет в таблицу данные
$spreadsheetId = '1dQgxqMHVwApxEp1ZqcODMlXgX3UmKzoBsh_RTuHt6Ss';

// Настройки запроса к Метрике
$date1 = 		'yesterday';
$date2 = 		'yesterday';
$metrics= 	'ym:s:visits,ym:s:users,ym:s:pageDepth,ym:s:bounceRate';
$dimensions='ym:s:date,ym:s:startURLDomain';
$sort= 			'ym:s:date,-ym:s:visits';

// пустой массив для отправки строк в Gsheet
$strArr 	= [];


	// взять последнюю дату из столбца A:A в целевой таблице

	[$data, $query] = sendRequest($date1,$date2,$group,$metrics,$dimensions,$sort);

	appendRows($data, $service, $spreadsheetId);