<?php
	// session_start();
	// include("../settings/connect_datebase.php");
	
	// $login = $_POST['login'];
	
	// // ищем пользователя
	// $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."';");
	
	// $id = -1;
	// if($user_read = $query_user->fetch_row()) {
	// 	// создаём новый пароль
	// 	$id = $user_read[0];
	// }
	
	// function PasswordGeneration() {
	// 	// создаём пароль
	// 	$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; // матрица
	// 	$max=10; // количество
	// 	$size=StrLen($chars)-1; // Определяем количество символов в $chars
	// 	$password="";
		
	// 	while($max--) {
	// 		$password.=$chars[rand(0,$size)];
	// 	}
		
	// 	return $password;
	// }
	
	// if($id != 0) {
	// 	//обновляем пароль
	// 	$password = PasswordGeneration();;
	// 	// проверяем не используется ли пароль 
	// 	$query_password = $mysqli->query("SELECT * FROM `users` WHERE `password`= '".md5($password)."';");
	// 	while($password_read = $query_password->fetch_row()) {
	// 		// создаём новый пароль
	// 		$password = PasswordGeneration();
	// 	}
	// 	// обновляем пароль
	// 	$mysqli->query("UPDATE `users` SET `password`='".md5($password)."' WHERE `login` = '".$login."'");
	// 	// отсылаем на почту
	// 	//mail($login, 'Безопасность web-приложений КГАПОУ "Авиатехникум"', "Ваш пароль был только что изменён. Новый пароль: ".$password);
	// }
	
	// echo $id;
	session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'];

$id = -1;

// ЗАЩИТА: Поиск пользователя с подготовленными выражениями
$query_user = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
$query_user->bind_param("s", $login);
$query_user->execute();
$query_user->bind_result($id);
$query_user->fetch();
$query_user->close();

function PasswordGeneration() {
    $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    $max = 10;
    $size = StrLen($chars) - 1;
    $password = "";
    
    while($max--) {
        $password .= $chars[rand(0, $size)];
    }
    
    return $password;
}

if($id != -1) {
    // Генерируем пароль
    $password = PasswordGeneration();
    
    // ЗАЩИТА: Проверка пароля с подготовленными выражениями
    $password_exists = true;
    while($password_exists) {
        $hashed_password = md5($password);
        $query_password = $mysqli->prepare("SELECT COUNT(*) FROM `users` WHERE `password` = ?");
        $query_password->bind_param("s", $hashed_password);
        $query_password->execute();
        $query_password->bind_result($count);
        $query_password->fetch();
        $query_password->close();
        
        if($count == 0) {
            $password_exists = false;
        } else {
            $password = PasswordGeneration();
        }
    }
    
    // ЗАЩИТА: Обновление пароля с подготовленными выражениями
    $update_query = $mysqli->prepare("UPDATE `users` SET `password` = ? WHERE `login` = ?");
    $update_query->bind_param("ss", $hashed_password, $login);
    
    if($update_query->execute()) {
        // Отправка email (раскомментируйте когда нужно)
        // mail($login, 'Безопасность web-приложений КГАПОУ "Авиатехникум"', "Ваш пароль был только что изменён. Новый пароль: ".$password);
    }
    
    $update_query->close();
}

echo $id;
?>