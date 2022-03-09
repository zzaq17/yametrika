<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors','1');

require_once '../../settings/config.php';



$content = file(__DIR__ . "\list_of_all_pages.txt");
$i = 1;
$dir = './htmls/';

foreach ($content as $url) {
	$url = trim($url);

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
	
	$file = $dir . $file_name . ".html";
	$fp = fopen($file, 'w');
	$ch = curl_init($url);
	print_r(curl_error($ch)."<br>");
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	$data = curl_exec($ch);
	print_r(curl_error($ch)."<br>");
	
	curl_close($ch);
	
	fclose($fp);
	
	// // $doc = new DOMDocument();
	// // $doc->loadHTML($save_file_loc);

	}
// foreach ($content as $url) {
// 	$fp = fopen(__DIR__ . $dir . $i, 'w');
// 	$ch = curl_init(trim($url));
// 	print_r(curl_error($ch)."<br>");
// 	curl_setopt($ch, CURLOPT_FILE, $fp);
// 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
// 	curl_setopt($ch, CURLOPT_HEADER, 0);
// 	// curl_setopt(CURLOPT_COOKIEFILE, CURLOPT_COOKIEJAR);
	

// 	$data = curl_exec($ch);
// 	print_r(curl_error($ch)."<br>");
	
// 	curl_close($ch);
	
// 	fclose($fp);
// 	$i++;
// } 
	


	// $fp = fopen(__DIR__ . '/htmls/ramenskoye.standartcleaning.ru.html', 'w');
	// $ch = curl_init('https://ramenskoye.standartcleaning.ru');
	// print_r(curl_error($ch)."<br>");
	// curl_setopt($ch, CURLOPT_FILE, $fp);
	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
	// curl_setopt($ch, CURLOPT_HEADER, 0);
	// // curl_setopt(CURLOPT_COOKIEFILE, CURLOPT_COOKIEJAR);
	

	// $data = curl_exec($ch);
	// print_r(curl_error($ch)."<br>");
	
	// curl_close($ch);
	
	// fclose($fp);