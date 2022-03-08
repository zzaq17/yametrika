<?php

require_once '../../settings/config.php';

$content = file(__DIR__ . "\list_of_all_pages.txt");

// Initialize a file URL to the variable
foreach ($content as $url) {
	// print_r($url);
	
	// Initialize the cURL session
	$ch = curl_init($url);
	
	// Initialize directory name where
	// file will be save
	$dir = './htmls/';
	
	// the base name of file
	$lastsym = strlen(trim($url))-1;
	
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
	$file_name = str_replace($search, $change, trim($url));
	
	// Save file into file location
	$save_file_loc = $dir . $file_name . ".html";

	print_r($save_file_loc . "<br>");

	// Open file
	$fp = fopen($save_file_loc, 'wb');

	// It set an option for a cURL transfer
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	// Perform a cURL session
	curl_exec($ch);

	// Closes a cURL session and frees all resources
	curl_close($ch);
	
	// Close file
	fclose($fp);
	$doc = new DOMDocument();
	$doc->loadHTML($save_file_loc);

	$xpath = new DOMXpath($doc);
	$inputs = $xpath->query('//title');
}

// print_r($inputs);
// print_r("<br><br>");	

  // all links in .blogArticle
  // $links = [];
  // foreach($inputs as $container) {
  //   $arr = $container->getElementsByTagName("title");
  //   foreach($arr as $item) {
  //     $href =  $item->getAttribute("value");
  //     $text = trim(preg_replace("/[\r\n]+/", " ", $item->nodeValue));
  //     $links[] = [
  //       'href' => $href,
  //       'text' => $text
  //     ];
  //   }
  // }

	// print_r($links);
