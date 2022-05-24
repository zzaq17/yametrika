<?php

// список url для парсинга
$fp = fopen('urls.txt', 'r');
$urls = [];

if ($fp) {
    while (($buffer = fgets($fp)) !== false) {
			$urls[] = trim($buffer);
    }
}
fclose($fp);

$newParagrafs = [];

// $newContent = 
foreach ($urls as $url) {
	$content = file_get_contents($url);
	print_r($content);
			// $content_table = explode("<p>", $content);
			// 			// echo $content[$i];
			// 		$content_table[7] .= $newParagraf . "</p>";
			// 	// echo $newContent;
			// $newContent = implode("<p>", $content_table);
			// 	$fileName = substr($url, 29, -1);
			// 	file_put_contents( "changed_pages/".$fileName.'.html', $newContent);
				}




$textToFind = 'холецистит';
$anchor = '<a href="#">'.$textToFind.'</a>';






// $newContent = str_replace($textToFind, $anchor, $content);


	echo "\n\n\t\t**** READY ****\n";