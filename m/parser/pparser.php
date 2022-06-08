<?php

use Google\Service\BigtableAdmin\Split;
echo "Start task at ".date('d.m.Y H:i:s');
$start = microtime(true);
$memory = memory_get_usage();

// 1 .Берем список url для парсинга
	$fp = fopen('startfiles/purls.txt', 'r');
	$purls = [];

	if ($fp) {
		while (($buffer = fgets($fp)) !== false) {
			$purls[] = trim($buffer);
		}
	}
	fclose($fp);

// 2. Берем фразы из семантики
	$fp = fopen('startfiles/semantic.txt', 'r');
	$semantic = [];

	if ($fp) {
		while (($buffer = fgets($fp)) !== false) {
			$semantic[] = trim($buffer);
		}
	}
	fclose($fp);

	$csvFileName = 'tvoyapechenka_paragrafs_'.date('d.m.y').'.csv';
	$handle = fopen('responses/'.$csvFileName, 'w');
	// fopen(__DIR__.'\\responses\\'.$csvFileName, 'w');
	fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
	if (($handle = fopen('responses/'.$csvFileName, 'w')) !== FALSE) {
		$csv_rows = [
			'Фраза',
			'Параграф с фразой',
			'URL источника',
		];
		
		fputcsv($handle, $csv_rows);
	}
	fclose($handle);

// 3. Запускаем поиск в каждом html параграфов содержащих фразу из сематники
	$headers = array(
		'cache-control: max-age=0',
		'upgrade-insecure-requests: 1',
		'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
		'sec-fetch-user: ?1',
		'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
		'x-compress: null',
		'sec-fetch-site: none',
		'sec-fetch-mode: navigate',
		'accept-encoding: deflate, br',
		'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
	);
	$competitorsParagrafs = [];
	$i = 1;

	foreach ($purls as $url) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt');
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, true);
		$content = curl_exec($ch);
		echo "\n\n №".$i." URL: ".$url;
		if($errno = curl_errno($ch)) {
			$error_message = curl_strerror($errno);
			echo "cURL at ".date('d.m.Y H:i:s')." error ({$errno}) on" .$url. ":\n {$error_message}\n";
		}
		curl_close($ch);
		$j = 1;
		foreach ($semantic as $phrase) {

			$phraseParts = explode(" ", $phrase);
			$parts = [];
			foreach ($phraseParts as $part) {
				$parts[] = substr($part, 1, -2);
			}
			$phraseRegexp = implode(".*?", $parts );
			// $pattern = "/\<p\>.*?".$phraseRegexp.".*?\<\/p\>/";
			$pattern = "/\<p\>.*?".$phrase.".*?\<\/p\>/";
			
			preg_match_all($pattern, $content, $matches);
			$h = 0;
		
			if (count($matches[0])>0) {
				foreach($matches[0] as $match => $paragraf) {
					if (in_array($paragraf, $competitorsParagrafs) == false) {
						$competitorsParagrafs[] = [
								$phrase,
								$paragraf,
								$url
							];
							print_r(' успешно!');
						$h = $h+1;

					}
				}
			}
			else {
			}
			$j++;
		}
		$i++;
		// задержка выполнения на 0,05 секунд
		// usleep(50000);
	}
	if (($handle = fopen('responses/'.$csvFileName, 'w')) !== FALSE) {
		foreach ($competitorsParagrafs as $row) {
			fputcsv($handle, $row);
		}
		fclose($handle);
		echo "\n\n\t\t\t Данные добавлены в файл!". date('d.m.Y H:i:s');	
	}





if((memory_get_usage()-$memory)>1024000) {
	echo "\n\n\n\n";
	echo 'Задействовано памяти: ' . (memory_get_usage() - $memory) . ' байт';
	echo "\n\n";
	echo 'Время выполнения скрипта: ' . round((microtime(true) - $start),3) . ' sec.';
}
	echo "\n\n\t\t**** READY ****\n";