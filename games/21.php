<?php

############################################################################################
##                                    Главная страница                                    ##
############################################################################################
if ($action==""){
echo 'В наличии: '.moneys($udata[41]).'<br /><br />';

if (empty($_SESSION['stavka'])){

if ($udata[41]>0){
echo 'Ваша ставка (1 - '.$config['ochkostavka'].'):<br />';

echo'<form action="21.php?action=ini&amp;rand='.$randgame.'&amp;'.SID.'" method="post">';
echo'<input name="mn" /><br />';
echo'<input type="submit" value="Играть" /></form><hr />';

} else {echo '<b>У вас нет денег для игры!</b><br /><br />';}

echo'Mаксимальная ставка - '.moneys($config['ochkostavka']).'<br /><br />';

} else {
echo 'Cтавки сделаны, на кону: '.moneys($_SESSION['stavka']*2).'<br /><br />';
echo '<b><a href="21.php?action=game&amp;act=go&amp;rand='.$randgame.'&amp;'.SID.'">Вернитесь в игру</a></b><br /><br />';
}

echo '<img src="../images/img/faq.gif" alt="image" /> <a href="21.php?action=pravila&amp;'.SID.'">Правила игры</a><br />';
}
