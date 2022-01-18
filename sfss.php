<?php

function sfss_to_csv($filename='', $delimiter=';') {
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;
	
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE) {
		fgetcsv($handle, 4000, $delimiter);
		while (($row = fgetcsv($handle, 4000, $delimiter)) !== FALSE) {
			$i = 1;
			$j = 2;
			// print_r($row[$i]);
			foreach ($row as $key) {
					if (strlen($row[$i]) > 0) {
					$url = $row[0];
					// print_r('<br>');
					$data[] = [
						[$url, $row[$i], $row[$j]],
					];
					// 	[$url, $row[3], $row[4]],
					// 	[$url, $row[5], $row[6]],
					// 	[$url, $row[7], $row[8]],
					// 	[$url, $row[9], $row[10]],
					// 	[$url, $row[11], $row[12]],
					// 	[$url, $row[13], $row[14]],
					// 	[$url, $row[15], $row[16]],
					// 	[$url, $row[17], $row[18]],
					// 	[$url, $row[19], $row[20]],
					// 	[$url, $row[21], $row[22]],
					// 	[$url, $row[23], $row[24]],
					// 	[$url, $row[25], $row[26]],
					// 	[$url, $row[27], $row[28]],
					// 	[$url, $row[29], $row[30]],
					// 	[$url, $row[31], $row[32]],
					// ];
					$i = $i+2;
					$j = $j+2;
				}
			}
		}
		fclose($handle);
	}
	
	$csvFileName = 'forms_RPM_'.date('d.m.y').'.csv';
	$handle = fopen(__DIR__.'\\responses\\'.$csvFileName, 'w');
	fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
	
	
	if (($handle = fopen(__DIR__.'\\responses\\'.$csvFileName, 'w')) !== FALSE) {
		$csv_rows = [
			'URL',
			'id формы',
			'Название формы',
		];
		
		fputcsv($handle, $csv_rows);

		foreach ($data as $row) {
				// print_r($data);
				// print_r($data);
				$r = $row[0];

				$csv_rows = [
					$r[0], 
					$r[1], 
					$r[2] 
				];

				fputcsv($handle, $csv_rows);

			}
			fclose($handle);
		}
	return $data;
}


sfss_to_csv("sfss/sfss.csv");
