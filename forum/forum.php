<?php
#-----------------------------------------------------#
#          ********* WAP-MOTORS *********             #
#              Made by  :  VANTUZ                     #
#               E-mail  :  visavi.net@mail.ru         #
#                 Site  :  http://pizdec.ru           #
#             WAP-Site  :  http://visavi.net          #
#                  ICQ  :  36-44-66                   #
#  Вы не имеете право вносить изменения в код скрипта #
#        для его дальнейшего распространения          #
#-----------------------------------------------------#
require_once ('../includes/start.php');
require_once ('../includes/functions.php');
require_once ('../includes/header.php');
include_once ('../themes/'.$config['themes'].'/index.php');

$act = (isset($_GET['act'])) ? check($_GET['act']) : 'index';
$start = (isset($_GET['start'])) ? abs(intval($_GET['start'])) : 0;
$fid = (isset($_GET['fid'])) ? abs(intval($_GET['fid'])) : 0;

show_title('menu.gif', 'Форум '.$config['title']);

switch ($act):
############################################################################################
##                             Вывод перечня тем в категории                              ##
############################################################################################
case "index":

	$forum = search_string(DATADIR."dataforum/mainforum.dat", $fid, 0);
	if ($forum) {

		$total = counter_string(DATADIR."dataforum/topic$fid.dat");

		echo '<a href="#down"><img src="../images/img/downs.gif" alt="image" /></a> ';
		echo '<a href="index.php?'.SID.'">Форум</a> / ';
		echo '<a href="forum.php?act=new&amp;fid='.$fid.'&amp;'.SID.'">Новая тема</a>';

		if (is_admin()){
			echo ' / <a href="'.ADMINDIR.'forum.php?act=forum&amp;fid='.$fid.'&amp;start='.$start.'&amp;'.SID.'">Управление</a>';
		}

		echo '<br /><br /><img src="../images/img/themes.gif" alt="image" /> <b>'.$forum[1].'</b> ('.$total.' тем.)<hr />';

		if ($total>0) {
			$files = file(DATADIR."dataforum/topic$fid.dat");
			$files = array_reverse($files);

			// Выводим сперва закрепленные темы
			$fixed = array();
			foreach ($files as $key=>$value){
				$data = explode("|", $value);
				if (!empty($data[5])){
					unset($files[$key]);
					$fixed[] = $value;
				}
			}
			$files = array_merge($fixed, $files);
			//-------------------------------//

			if ($start < 0 || $start >= $total){$start = 0;}
			if ($total < $start + $config['forumtem']){ $end = $total; }
			else {$end = $start + $config['forumtem']; }
			for ($i = $start; $i < $end; $i++){

				$data = explode("|", $files[$i]);

				if ($data[5] == 1) {
					$icon = 'lock.gif';
				} elseif($data[6] == 1) {
					$icon = 'close.gif';
				} else {
					$icon = 'forums.gif';
				}

				$totaltopic = counter_string(DATADIR."dataforum/".$data[0].".dat");

				echo '<div class="b"><img src="../images/img/'.$icon.'" alt="image" /> ';
				echo '<b><a href="topic.php?fid='.$fid.'&amp;id='.$data[0].'&amp;'.SID.'">'.$data[3].'</a></b> ('.$totaltopic.')</div>';

				if($totaltopic>0){
					$filetop = file(DATADIR."dataforum/".$data[0].".dat");
					$lostlist = explode("|", end($filetop));

					$totpage = counter_string(DATADIR."dataforum/".$lostlist[0].".dat");
					$lastpage = ceil($totpage/$config['forumpost']) * $config['forumpost'] - $config['forumpost'];

					echo '<div>Страницы: ';
					forum_navigation('topic.php?fid='.$lostlist[1].'&amp;id='.$lostlist[0].'&amp;', $config['forumpost'], $totaltopic);

					echo 'Сообщение: '.nickname($lostlist[2]).' ('.date_fixed($lostlist[6]).')</div>';

				} else {echo 'Тема пустая! Сообщений еще нет!';}
			}

			page_strnavigation('forum.php?fid='.$fid.'&amp;', $config['forumtem'], $start, $total);


		} else {show_error('Форум пустой! Разделы еще не созданы!');}
	} else {show_error('Ошибка! Данного раздела не существует!');}
break;


############################################################################################
##                           Подготовка к созданию новой темы                             ##
############################################################################################
case 'new':

	if (is_user()) {
		$forums = file(DATADIR."dataforum/mainforum.dat");

		if (count($forums) > 0) {

			echo '<div class="form">';
			echo '<form action="forum.php?act=create&amp;fid='.$fid.'&amp;uid='.$_SESSION['token'].'&amp;'.SID.'" method="post">';

			echo 'Раздел:<br />';

			echo '<select name="fid">';

			foreach ($forums as $data) {
				$data = explode("|", $data);

				$selected = ($fid == $data[0]) ? ' selected="selected"' : '';
				echo '<option value="'.$data[0].'"'.$selected.'>'.$data[1].'</option>';
			}

			echo '</select><br />';

			echo 'Заголовок:<br />';
			echo '<input type="text" name="title" size="50" maxlength="50" /><br />';
			echo 'Сообщение:<br />';
			echo '<textarea cols="25" rows="5" name="msg" id="msg"></textarea><br />';
			echo '<input value="Создать тему" type="submit" /></form></div><br />';

			echo 'Прежде чем создать новую тему необходимо ознакомиться с правилами<br />';
			echo '<a href="../pages/pravila.php?'.SID.'">Правила сайта</a><br />';
			echo 'Также убедись что такой темы нет, чтобы не создавать одинаковые, для этого введи ключевое слово в поиске<br />';
			echo '<a href="search.php?'.SID.'">Поиск по форуму</a><br />';
			echo 'И если после этого вы уверены, что ваша тема будет интересна другим пользователям, то можете ее создать<br /><br />';
		} else {
			show_error('Разделы форума еще не созданы!');
		}
	} else {
		show_login('Вы не авторизованы, для создания новой темы, необходимо');
	}

	echo '<img src="../images/img/back.gif" alt="image" /> <a href="forum.php?fid='.$fid.'&amp;'.SID.'">Вернуться</a><br />';
break;

############################################################################################
##                                  Создание новой темы                                   ##
############################################################################################
case 'create':

	$forum = search_string(DATADIR."dataforum/mainforum.dat", $fid, 0);
	if ($forum) {

		$title = check($_POST['title']);
		$msg = check($_POST['msg']);

		//antiflood("Location: forum.php?fid=$fid&isset=antiflood&".SID);
		karantin($udata[6], "Location: forum.php?fid=$fid&isset=karantin&".SID);

		if (utf_strlen(trim($title))>=5 && utf_strlen($title)<=50){
			if (utf_strlen(trim($msg))>=5 && utf_strlen($msg)<=3000){

				$title = no_br($title);
				$msg = no_br($msg,'<br />');

				$id = unifile(DATADIR."dataforum/topic$fid.dat", 0);

				// Создание темы в списке тем
				$text = $id.'|'.$fid.'|'.$log.'|'.$title.'|'.SITETIME.'|0|0|';
				write_files(DATADIR."dataforum/topic$fid.dat", "$text\r\n", 0, 0666);

				// Создание файла темы и запись сообщения
				$topictext = $id.'|'.$fid.'|'.$log.'|'.$title.'|'.$msg.'|'.$brow.', '.$ip.'|'.SITETIME.'|0|0|';
				write_files(DATADIR."dataforum/$id.dat", "$topictext\r\n", 1, 0666);

				// Обновление mainforum
				$maintext = $forum[0].'|'.$forum[1].'|'.($forum[2]+1).'|'.($forum[3]+1).'|';
				replace_lines(DATADIR."dataforum/mainforum.dat", $forum['line'], $maintext);

				//Удаление старых тем
				$file = file(DATADIR."dataforum/topic$fid.dat");
				if (count($file)>$config['topforum']){
					foreach ($file as $key => $value) {
						$data = explode("|", $value);
						if (empty($data[5])){ // если тема не закреплена

							if (file_exists(DATADIR."dataforum/".$data[0].".dat")) {
								unlink (DATADIR."dataforum/".$data[0].".dat");
							}

							unset($file[$key]);
							file_put_contents(DATADIR."dataforum/topic$fid.dat", $file, LOCK_EX);
							break;
						}
					}
				}

				//header ("Location: topic.php?fid=$fid&id=$id&isset=oktem&".SID);	exit;

			} else {show_error('Слишком длинный или короткий текст сообщения (Необходимо от 5 до 3000 символов)');}
		} else {show_error('Слишком длинный или короткий заголовок (Необходимо от 5 до 50 символов)');}

	} else {
		show_error('Ошибка! Даннго раздела для создании темы не существует!');
	}

echo '<img src="../images/img/back.gif" alt="image" /> <a href="forum.php?act=new&amp;fid='.$fid.'&amp;'.SID.'">Вернуться</a><br />';
break;

default:
header("location: index.php?".SID); exit;
endswitch;

echo '<img src="../images/img/homepage.gif" alt="image" /> <a href="../index.php?'.SID.'">На главную</a><br />';

include_once ('../themes/'.$config['themes'].'/foot.php');
?>