<?php
	session_start();
	include("../settings/connect_datebase.php");
	
	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// ищем пользователя
	// $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
	
	// $id = -1;
	// while($user_read = $query_user->fetch_row()) {
	// 	$id = $user_read[0];
	// }
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
	$id = -1;

// Правильное использование prepare и bind_param
$query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ? AND `password` = ?");
$query->bind_param("ss", $login, $password);
$query->execute();
$query->bind_result($id);
$query->fetch();
$query->close();

if($id != -1) {
    $_SESSION['user'] = $id;
}
echo md5(md5($id));
?>