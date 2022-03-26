<?php 

require '../../vendor/autoload.php';

$today = date('d.m.y');
$now = date('d.m.y_H.m.s');

$counterIDs = [
	'laparoskopiya.ru' => '50212432',
	'gryzha-operaciya.ru' => '50337838',
	'gernioplastika.ru' => '56942572',
	'bezdiastaza.ru' => '57655366',
	'klinikaboli.ru' => '48002045',
	'port-sistema.ru' => '46733565',
	'endometryoz.ru' => '62131978',
	// 'puzyr.info ' => '51610628',
	'udalenie-zhelchnogo.ru' => '49221070',
	// 'laparoscope.ru' => '72684979',
	'paingkb.ru' => '46696410',
	// 'amuletobereg.ru' => '56530147',
	'udalenie-gemorroya.ru' => '51933194',
	'k31orto.ru' => '49052279',
	// 'pechen.infox.ru' => '54754291',
	'flebologcentr.ru' => '87612744',
];

$ids = implode(',',$counterIDs);

$reports = [
	'goals' => 'goals',
	'goal' => 'goal',
];

$goalTypes = [
	'number' => 'количество просмотров',
	'action' => 'JavaScript-событие',
	'step' => 'составная цель',
	'url' => 'по URL страницы',
	'email' => 'клик по email',
	'phone' => 'клик по номеру телефона',
	'form' => 'отправка формы',
	'messenger' => 'переход в мессенджер',
	'button' => 'клик по кнопке',
	'file' => 'скачивание файла',
	'search' => 'поиск по сайту',
	'call' => 'звонок',
	'conditional_call' => 'целевой звонок',
];

$goalCondsTypes = [
	'regexp' => 'удовлетворяет регулярному выражению',
	'contain' => 'содержит',
	'start' => 'начинается с',
	'exact' => 'совпадает',
	'action' => 'специальный тип условия для целей JavaScript-событие',
];

$gsheetID = '1mcLor5uxdnlowPvLAVSWGl_MiSuD76bb4UZ8JC98RbI';

