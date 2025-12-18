<?php
	session_start();
	include("../settings/connect_datebase.php");
	
	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
	$id = -1;
	
	if($user_read = $query_user->fetch_row()) {
		echo $id; // пользователь уже существует
	} else {
		// регистрируем нового пользователя
		$mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login."', '".$password."', 0)");
		
		$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
		$user_new = $query_user->fetch_row();
		$id = $user_new[0];
		
		if($id != -1) {
			$_SESSION['user'] = $id; // запоминаем пользователя
			
			$Ip = $_SERVER["REMOTE_ADDR"];
			$DateStart = date("Y-m-d H:i:s");
	
			// создаем запись в таблице session
			$Sql = "INSERT INTO `session`(`IdUser`, `Ip`, `DateStart`, `DateNow`) VALUES ({$id}, '{$Ip}', '{$DateStart}', '{$DateStart}')";
			$mysqli->query($Sql);
	
			// получаем ID созданной сессии
			$Sql = "SELECT `Id` FROM `session` WHERE `DateStart` = '{$DateStart}' AND `IdUser` = {$id}";
			$Query = $mysqli->query($Sql);
			$Read = $Query->fetch_assoc();
			$_SESSION["IdSession"] = $Read["Id"];
	
			// логируем событие регистрации
			$Sql = "INSERT INTO ".
			"`logs`(`Ip`, `IdUser`, `Date`, `TineOnline`, `Event`) ".
			"VALUES ('{$Ip}','{$id}','{$DateStart}','00:00:00','Зарегистрирован новый пользователь {$login}')";
			$mysqli->query($Sql);
		}
		echo $id;
	}

// session_start();
// include("../settings/connect_datebase.php");

// $login = $_POST['login'];
// $password = $_POST['password'];

// $id = -1;

// $query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
// $query->bind_param("s", $login);
// $query->execute();
// $query->bind_result($id);
// $query->fetch();
// $query->close();

// if($id != -1) {
//     echo $id;
// } else {
//     $query = $mysqli->prepare("INSERT INTO `users`(`login`, `password`, `roll`) VALUES (?, ?, 0)");
//     $query->bind_param("ss", $login, $password);
//     $query->execute();
//     $query->close();
    
//     $query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ? AND `password` = ?");
//     $query->bind_param("ss", $login, $password);
//     $query->execute();
//     $query->bind_result($id);
//     $query->fetch();
//     $query->close();
        
//     if($id != -1) {
//         $_SESSION['user'] = $id;
//     }
//     echo $id;
// }
?>
