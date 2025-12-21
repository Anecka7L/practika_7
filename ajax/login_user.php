<?php
	session_start();
	include("../settings/connect_datebase.php");
	include("../settings/log_functions.php");

	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
	
	$id = -1;
	while($user_read = $query_user->fetch_row()) {
		$id = $user_read[0];
	}
	// $id = -1;
	// $Query = $mysqli->prepare(query: "SELECT `id` FROM `users` WHERE `login` = ? AND `password` = ?;");
	// $Query->bind_param(
	// 	types: "ss",
	// 	var: &$login,
	// 	vars: &$password);
	// 	$Query->execute();
	// 	$Query->bind_result(var: &$id);
	// 	$Query->fetch();
	// if($id != -1) {
	// 	$_SESSION['user'] = $id;
	// }
	// echo md5(string: md5(string: $id));
	// $id = -1;

// Правильное использование prepare и bind_param
// $query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ? AND `password` = ?");
// $query->bind_param("ss", $login, $password);
// $query->execute();
// $query->bind_result($id);
// $query->fetch();
// $query->close();

if($id != -1) {
    $_SESSION['user'] = $id;

	$Ip = $_SERVER["REMOTE_ADDR"];
	$DateStart = date(format: "Y-m-d H:i:s");

	logToFile("Успешная авторизация пользователя: $login", $id);

	$Sql = "INSERT INTO `session`(`IdUser`, `Ip`, `DateStart`, `DateNow`) VALUES ({$id}, '{$Ip}', '{$DateStart}', '{$DateStart}')";
	$mysqli->query(query: $Sql);

	$Sql = "SELECT `Id` FROM `session` WHERE `DateStart` = '{$DateStart}';";
	$Query = $mysqli->query(query: $Sql);
	$Read = $Query->fetch_assoc();
	$_SESSION["IdSession"] = $Read["Id"];

	$Sql = "INSERT INTO ".
	"`logs`(`Ip`, `IdUser`, `Date`, `TineOnline`, `Event`) ".
	"VALUES ('{$Ip}','{$id}','{$DateStart}','00:00:00','Пользователь {$login} авторизовался.')";
	$mysqli->query(query: $Sql);
}
else {
    logToFile("Неудачная попытка входа. Логин: $login");
}
  echo md5(md5($id));
?>