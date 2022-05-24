<?php


// 1. собираем массив анкоров и ссылок из csv
$fileRelev = 'startfiles/relevans.csv';
if(!file_exists($fileRelev) || !is_readable($fileRelev))
return FALSE;

$relevanse = [];
if (($handle = fopen($fileRelev, 'r')) !== FALSE) {
	fgetcsv($handle, 10000, ";");
	while (($row = fgetcsv($handle, 10000, ";")) !== FALSE) {
		// print_r($row);
		// print_r('<br>');
		// 	print_r($row[0] ." => ". $row[1]);
		// 	print_r('<br>');
		$relevanse[$row[0]] = $row[1];
		// $relevanse = [
			// 	$row[0] => $row[1],
			// ];
		}
		fclose($handle);
	}
	
	// 2. Ищем совпадения фраз с анкорами и заменяем в csv с фразами
	
	$result=[];
	$fileParag = 'responses/forancore.csv';
	$fileResult = 'responses/full_result.csv';
	if(!file_exists($fileResult) || !is_readable($fileResult))
	return FALSE;
	
	if (($fp = fopen($fileResult, 'w')) !== FALSE) {
		$fp = fopen($fileResult, 'w');
		print_r('файл для записи открыт <br>');
		$headers = [
			'Фраза',
			'Целевой URL',
			'Блок с анкором',
			'Параграф с анкором',
			'Страница для размещения'
		];
		fputcsv($fp, $headers);
	}
	fclose($fp);
	
	if(!file_exists($fileParag) || !is_readable($fileParag))
	return FALSE;
	
	if (($handle = fopen($fileParag, 'r')) !== FALSE) {
		fgetcsv($handle, 10000, ",");
		while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
		$phrase = $row[0];
		$paragraf = trim($row[1]);
		
		print_r("<br><br>Фраза - ".$phrase);
		print_r("<br>Параграф - ".$paragraf);
		
		foreach ($relevanse as $ancore => $url) {
			print_r('<br>'.$ancore);
			print_r(' - '.$phrase);
			if ($ancore == $phrase) {
				print_r("<br>Совпадение - ".$ancore. " = ".$phrase);
				$include = '<a href="'.$url.'">'.$ancore.'</a>';
				// print_r(''.$include);
				$pos = strpos($paragraf,$ancore);
				if ($pos !== false) {
					$newParagraf = substr_replace($paragraf,$include,$pos,strlen($ancore));
					$result = [
						$phrase,
						$url,
						$include,
						$newParagraf
					];
					print_r($result);
					if(!file_exists($fileResult) || !is_readable($fileResult))
					return FALSE;
					if (($fp = fopen($fileResult, 'w')) !== FALSE) {
					fputcsv($fp, $result);
					fclose($fp);
				}
				else {
					print_r('<br><br>**********************Проблемы с открытием файла для результата***************************');
				}
				}
			}
			else {}
			
		}
	}
	fclose($handle);
}
