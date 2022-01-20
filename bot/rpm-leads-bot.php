<?php
	require_once 'botconfig.php';

	$bot_api = 'https://api.telegram.org/bot'.$bot_access_token;

		$input_array = json_decode(file_get_contents('php://input'),TRUE);

		if($input_array){
			$chat_id = $input_array['message']['chat']['id'];
			$message = $input_array['message']['text'];
			$user_id = $input_array['message']['from']['id'];  // выделяем идентификатор юзера
			$fname = $input_array['message']['chat']['first_name']; // выделяем имя собеседника
			$lname = $input_array['message']['chat']['last_name'];  // выделяем фамилию собеседника
			$uname = $input_array['message']['chat']['username'];   // выделяем ник собеседника
						// обрабатываем принятое сообщение для защиты и удобства
			$message = trim($message);                         // удаляем пробелы
			$message = htmlspecialchars($message, ENT_QUOTES); // преобразуем спецсимволы (&, ", ', <, >) в html-сущности


			if(!$user_id){    // если в сообщении нет иденификатора юзера
					exit();       // завершаем работу скрипта
			}
			
			// пытаемся подключиться к БД
			$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
			if ($mysqli->connect_errno) {	// если подключиться не получилось - сообщаем админу
					sendMessage($admin_chat_id,'Не удалось подключиться к БД ('.$mysqli->connect_errno.': '.$mysqli->connect_error.') для пользователя с user_id: '.$user_id);
					exit();
			}
			
			$sel_number = 4;    // номер интересующего нас запроса (нам интересен ответ на запрос SELECT status)
			$sql = "";          // здесь формируем строку мультизапроса
			// запрос 1 - выставляем кодировку
			$sql .= 'SET NAMES utf8;';
			// запрос 2 - добавляем юзера в базу
			$sql .= 'INSERT IGNORE INTO MYTABLE (user_id, first_name) VALUES("'.$user_id.'", "'.$fname.'");';
			// запрос 3 - обновляем данные юзера в базе
			$sql .= 'UPDATE IGNORE MYTABLE SET first_name="'.$fname.'", last_name="'.$lname.'", nick_name="'.$uname.'" WHERE user_id="'.$user_id.'";';
			// запрос 4 - запрос на поиск юзера в базе
			$sql .= 'SELECT status FROM MYTABLE WHERE user_id="'.$user_id.'";';
			
			if (!$mysqli->multi_query($sql)) {    // пытаемся выполнить мультизапрос
					sendMessage($admin_chat_id,'Не удалось выполнить мультизапрос ('.$mysqli->errno.': '.$mysqli->error.') для пользователя с user_id: '.$user_id);
					exit();    // подключение отвалится само при завершении скрипта
			}
			
			$status = NULL;    // вначале статус юзера нам неизвестен
			$counter = 0;      // инициализируем счётчик обрабатываемых результатов
			do {    $counter += 1;                      // увеличиваем счётчик
							$res = $mysqli->store_result();     // получаем результат i-того запроса
							if ($res){                          // если результат запроса не нулевой
									if($counter == $sel_number){    // если это запрос select status
											$row = $res->fetch_row();   // получаем первую строку ответа
											// (она у нас всего одна должна быть, мы же ищем по user_id, а он уникальный)
											if(isset($row[0])){         // если нулевой элемент этой строки существует
											// (значение status, поскольку мы запрашивали только его),
													$status = $row[0];      // то статус юзера равен прочитанному
											}
									}
									$res->free();                   // если результат был не пустой, то освобождаем его
							}
			} while ($mysqli->more_results() && $mysqli->next_result()); //перебираем все результаты мультизапроса
			$mysqli->close();    // закрываем подключение к базе
			
			// проверяем что у нас получилось
			if(!isset($status)){ // если статус юзера не определён
					sendMessage($admin_chat_id,'Статус юзера с user_id = '.$user_id.' остался неопределёным.');
					exit();
			}
			elseif(!$status){    // если статус юзера равен нулю (юзер забанен)
					sendMessage($chat_id,'Для Вас заблокирована возможность посылать сообщения роботу @BOTNAME');
					exit();
			// начинаем распарсивать полученное сообщение
				$command = '';          // команды нет
				$user_chat_id = '';     // адресат не определён
				$user_text = '';        // текст от юзера пустой
				$admin_text = '';       // текст сообщения от админа тоже пустой
				
				$message_length = strlen($message);   // определяем длину сообщения
				if($message_length!=0){               // если сообщение не нулевое
						$fs_pos = strpos($message,' ');   // определяем позицию первого пробела
						if($fs_pos === false){            // если пробелов нет,
								$command = $message;          //  то это целиком команда, без текста
						}
						else{                             // если пробелы есть,
								// выделяем команду и текст
								$command = substr($message,0,$fs_pos);
								$user_text = substr($message,$fs_pos+1,$message_length-$fs_pos-1);
					
								$user_text_length = strlen($user_text);    // определяем длину выделенного текста
								// если команда от админа и после неё есть текст - продолжаем парсить
								if(($chat_id == $admin_chat_id) && (($command === '/send') || ($command === '/ban') || ($command === '/unban')) && ($user_text_length!=0)){
										// определяем позицию второго пробела
										$ss_pos = strpos($user_text,' ');
										if($ss_pos === false){                 // если второго пробела нет
												$user_chat_id = $user_text;        // то это целиком id чата назначения,
												$user_text = '';                   // а user_text - пустой
							}
										else{                     // если пробелы есть
												// выделяем id чата назначения и текст
												$user_chat_id = substr($user_text,0,$ss_pos);
												$admin_text = substr($user_text,$ss_pos+1,$user_text_length-$ss_pos-1);
										}
								}
						}
				}
					
				// после того, как всё распарсили, - начинаем проверять и выполнять
				switch($command){
						case('/start'):
							sendMessage($username, "Ваш cID" . $chat_id);
						case('/help'):
								sendMessage($chat_id,'Здравствуйте! Я робот RPMGroup. Я знаю такие команды:
										/help - вывести список поддерживаемых команд
										/send <i>message</i> - послать <i>message</i> админу');
								// если это команда от админа, дописываем что можно только ему
								if($chat_id == $admin_chat_id){
										sendMessage($chat_id,'Поскольку вы админ, то можно ещё вот это:
										/send <i>chat_id</i> <i>message</i> - послать <i>message</i> в указанный чат
										/ban <i>user_id</i> - забанить пользователя с указанным user_id
										/unban <i>user_id</i> - разбанить пользователя с указанным user_id');
								}
						break;
						case('/send'):    // отсылаем админу id чата юзера и его сообщение
								if($chat_id == $admin_chat_id){
										// посылаем текст по назначению (в указанный user_chat)
										sendMessage($user_chat_id, $admin_text);
								}
								else{
										sendMessage($admin_chat_id,$chat_id.': '.$user_text);
								}
						break;
						// команда /whoami добавлена чтобы админ мог узнать и записать
						// id своего чата с ботом, после этого её можно стереть
						case('/whoami'):
								sendMessage($chat_id,$chat_id);    // отсылаем юзеру id его чата с ботом
						break;
						case('/ban'):
								if($chat_id == $admin_chat_id){             // если это команда от админа
										if($user_chat_id != $admin_chat_id){    // если админ не пытается забанить сам себя
												// пытаемся подключиться к БД
												$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
												if ($telegram_mysqli->connect_errno) {
														sendMessage($admin_chat_id,'Не удалось подключиться к БД ('.$mysqli->connect_errno.': '.$mysqli->connect_error.') для пользователя с user_id: '.$user_id);
														exit();
												}
												// формируем запрос на обновление статуса указанного юзера в базе (меняем его на 0)
												$sql = 'UPDATE IGNORE MYTABLE SET status="0" WHERE user_id="'.$user_chat_id.'";';
												if (!$mysqli->multi_query($sql)) {  // пытаемся выполнить мультизапрос
														sendMessage($admin_chat_id,'Не удалось добавить в бан пользователя c user_id = '.$user_chat_id);
														exit();                         // подключение отвалится само при завершении скрипта
												}
												$mysqli->close();                   // закрываем подключение к базе
												sendMessage($admin_chat_id,'Запрос на добавление в бан пользователя c user_id = '.$user_chat_id.' выполнен');
										}
										else{                                   // если всё же админ пытается забанить сам себя
												sendMessage($admin_chat_id,'Никто не имеет права банить админа, даже сам админ!');
										}
								}
								else{
										sendMessage($chat_id,'неизвестная команда'); // если команда не от админа, то её как бы нет
								}
						break;
						case('/unban'):
								if($chat_id == $admin_chat_id){             // если это команда от админа
										// пытаемся подключиться к БД
										$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
										if ($mysqli->connect_errno) {
												sendMessage($admin_chat_id,'Не удалось подключиться к БД ('.$mysqli->connect_errno.': '.$mysqli->connect_error.') для пользователя с user_id: '.$user_id);
												exit();
										}
										// формируем запрос на обновление статуса юзера в базе (меняем его на 1)
										$sql = 'UPDATE IGNORE MYTABLE SET status="1" WHERE user_id="'.$user_chat_id.'";';
										if (!$mysqli->multi_query($sql)) {      // пытаемся выполнить мультизапрос
												sendMessage($admin_chat_id,'Не удалось выполнить отмену бана пользователя c user_id = '.$user_chat_id);
												exit();
										}
										$mysqli->close();                       // закрываем подключение к базе
										sendMessage($admin_chat_id,'Запрос на отмену бана пользователя c user_id = '.$user_chat_id.' выполнен');
								}
								else{
										sendMessage($chat_id,'неизвестная команда'); // если команда не от админа, то её как бы нет
								}
						break;
						default:
								sendMessage($chat_id,'неизвестная команда');
						break;
				}
		}
		else {
			print("Hello, I am bot! My name is @RPMGroup_bot & I'm whatching you)))\n");
		}
			/* Функция отправки сообщения в чат с использованием метода sendMessage*/
			function sendMessage($var_chat_id,$var_message){
					file_get_contents($GLOBALS['bot_api'].'/sendMessage?chat_id='.$var_chat_id.'&text='.urlencode($var_message));
			}
					// 'https://api.telegram.org/bot5101978790:AAHkRI1KXylf_-9wkjjwBS5SvtkefOGuZqI/setWebhook?url=https://webhook.testboxe.ru/bot/rpm-leads-bot.php'


