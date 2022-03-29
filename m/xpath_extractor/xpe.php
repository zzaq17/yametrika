<?php
require_once '../../settings/config.php';

function saveHtmls($content,$dir) {

	foreach ($content as $url) {
			// 1. Очистка url от непечатаемых знаков
			$url = trim($url);

			// 2. Обработка url в корректный вид для Windows файлов
			$lastsym = strlen($url)-1;

			if ($url[$lastsym] != "/") {
				$search = array("https://", ".ru/");
				$change   = array("", "_");
			}
			elseif ($url[$lastsym-3] != ".") {
				$search = array("https://", ".ru/", "/");
				$change   = array("", "_", "");
			}
			else {
				$search = array("https://", ".ru/");
				$change   = array("", "", );
			}
			$file_name = str_replace($search, $change, $url);
			// Присваивание имени файла
			$file = $dir . $file_name . ".html";
			
			// 4. Открытие файла и загрузка в него html данных по URL через curl
			$fp = fopen($file, 'w');
				$ch = curl_init($url);
				print_r(curl_error($ch)."<br>");
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				
				curl_exec($ch);
				print_r(curl_error($ch)."<br>");
				curl_close($ch);
			// 5. Закрытие файла
			fclose($fp);
	}

	return true;
};

$htmls 	= [];
$dest 	= fopen(__DIR__ . "\results\result.csv", "w");

function find_xpath($htmls,$dest) {


	return true;
};