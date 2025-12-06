<?php
	// session_start();
	// include("../settings/connect_datebase.php");
	
	// $login = $_POST['login'];
	// $password = $_POST['password'];
	
	// // ищем пользователя
	// $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
	// $id = -1;
	
	// if($user_read = $query_user->fetch_row()) {
	// 	echo $id;
	// } else {
	// 	$mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login."', '".$password."', 0)");
		
	// 	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
	// 	$user_new = $query_user->fetch_row();
	// 	$id = $user_new[0];
			
	// 	if($id != -1) $_SESSION['user'] = $id; // запоминаем пользователя
	// 	echo $id;
	// }

session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'];
$password = $_POST['password'];

$id = -1;

$query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
$query->bind_param("s", $login);
$query->execute();
$query->bind_result($id);
$query->fetch();
$query->close();

if($id != -1) {
    echo $id;
} else {
    $query = $mysqli->prepare("INSERT INTO `users`(`login`, `password`, `roll`) VALUES (?, ?, 0)");
    $query->bind_param("ss", $login, $password);
    $query->execute();
    $query->close();
    
    $query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ? AND `password` = ?");
    $query->bind_param("ss", $login, $password);
    $query->execute();
    $query->bind_result($id);
    $query->fetch();
    $query->close();
        
    if($id != -1) {
        $_SESSION['user'] = $id;
    }
    echo $id;
}
?>
