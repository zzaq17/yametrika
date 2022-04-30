<?php


//  Метрика
$token = 'AQAAAAA9EfgXAAeamBVa8-xocE1ipCqpfhmUlDo';

// заголовки запроса
$headers = [
	'POST:/management/v1/counters HTTP/1.1',
	'Host:api-metrika.yandex.net',
	'Authorization:OAuth '. $token ,
	'Content-Type:application/x-yametrika+json',
]; 


// keysso

// $keysToken = '621c774da83b08.088233619875910d15bafa7bc6903b4c7d3bdbf8';

$keysHeaders = [
	'X-Keyso-TOKEN:621c774da83b08.088233619875910d15bafa7bc6903b4c7d3bdbf8',
];