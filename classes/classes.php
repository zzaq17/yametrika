<?php
require_once 'settings/config.php';

class csvFile
{
    // объявление свойства
    public $csvFileName;
    public $csvheaders;

		

		public function __construct() {
			global $today;
			$this->csvFileName = 'goals_RPM_'.$today.'.csv';
		}
		
    public function csvCreate($csvheaders) {
			$buffer = fopen(__DIR__.'\\..\\responses\\'.$this->csvFileName, 'w');
			fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));
			fputcsv($buffer, $csvheaders);
			fclose($buffer);
		}

		public function csvAddRows($csvRow) {
			$buffer = fopen(__DIR__.'\\..\\responses\\'.$this->csvFileName, 'a');
			fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));
			fputcsv($buffer, $csvRow);
			fclose($buffer);
		}
}
