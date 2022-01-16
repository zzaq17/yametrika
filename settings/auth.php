<?php

$token = 'AQAAAAA9EfgXAAeamBVa8-xocE1ipCqpfhmUlDo';

// заголовки запроса
$headers = [
	'POST:/management/v1/counters HTTP/1.1',
	'Host:api-metrika.yandex.net',
	'Authorization:OAuth '. $token ,
	'Content-Type:application/x-yametrika+json',
]; 


