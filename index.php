<?php 
	// require_once 'app.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Список целей</title>
	<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="css/main.css">
	<script src="main.js"></script>
</head>
<body>
	<section>
		<h1>Скрипты для аналитики</h1>
		<div class="start-script">
			<div class="description">
				<p>Выгрузить цели метрики в CSV</p>
			</div>
				<button id="csv-script" class="btn">Старт!</button>
			<div class="ready-csv"></div>
		</div>
		<div class="start-script">
			<div class="description">
				<p>Обновить таблицу GSheet</p>
			</div>
			<a href="modules/forms_goals_extractions/gsheets.php">
				<button id="gsheet-script" class="btn">Старт!</button>
			</a>
			<div class="ready-gsheet"></div>
		</div>

	</section>
</body>
</html>