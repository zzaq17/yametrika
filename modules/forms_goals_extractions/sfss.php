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

			foreach ($row as $key) {
					if (strlen($row[$i]) > 0) {
					$url = $row[0];
					$data[] = [
						[$url, $row[$i], $row[$j]],
					];
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
