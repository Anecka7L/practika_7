<?php
session_start();
include("../settings/connect_datebase.php");
// define('ENCRYPTION_KEY', 'your-32-char-secret-key-here!123');
// function decryptToken($token) {
//     try {
//         $data = base64_decode($token);
//         $iv = substr($data, 0, 16);
//         $encrypted = substr($data, 16);
//         $decrypted = openssl_decrypt(
//             $encrypted,
//             'AES-256-CBC',
//             ENCRYPTION_KEY,
//             0,
//             $iv
//         );
//         return json_decode($decrypted, true);
//     } catch (Exception $e) {
//         return null;
//     }
// }
// function validateEncryptedToken($token) {
//     if (!$token) {
//         return false;
//     }
    
//     $decrypted = decryptToken($token);
//     if (!$decrypted) {
//         return false;
//     }
    
//     if (!isset($decrypted['user_id'], $decrypted['timestamp'], $decrypted['session_id'])) {
//         return false;
//     }
    
//     if (!isset($_SESSION['user']) || $decrypted['user_id'] != $_SESSION['user']) {
//         return false;
//     }
    
//     if ($decrypted['session_id'] !== session_id()) {
//         return false;
//     }
    
//     if (time() - $decrypted['timestamp'] > 900) {
//         return false;
//     }
    
//     return true;
// }
if (!isset($_SESSION['user'])) {
    die("Ошибка: пользователь не авторизован");
}

// if (!isset($_POST['encrypted_token']) || !validateEncryptedToken($_POST['encrypted_token'])) {
//     http_response_code(403);
//     die("Ошибка безопасности: невалидный CSRF токен");
// }
// $SECRET_KEY = "server.permaviat.ru";
$IdUser = $_SESSION['user'];
// $CSRF = $_POST["CSRF"];
$Message = $_POST["Message"];
$IdPost = $_POST["IdPost"];
$IdSession = $_SESSION["IdSession"];

// if($CSRF !=$_SESSION["CSRF"])
// exit;



$Sql = "SELECT `session`. *, `users`.`login` ".
	"FROM `session` `session` ".
	"JOIN `users` `users` ON `users`.`id` = `session`.`IdUser` ".
	"WHERE `session`.`Id` = {$IdSession}";

	$Query = $mysqli->query(query: $Sql);
	$Read = $Query->fetch_array();

	$TimeStart = strtotime(datetime: $Read["DateStart"]);
	$TimeNow = time();
	$Ip = $Read["Ip"];
	$TimeDelta = gmdate(format: "H:i:s", timestamp: ($TimeNow - $TimeStart));
	$Date = date(format: "Y-m-d H:i:s");
	$Login = $Read["login"];

	$Sql = "INSERT INTO ".
	"`logs`(`Ip`, `IdUser`, `Date`, `TineOnline`, `Event`) ".
	"VALUES ('{$Ip}','{$IdUser}','{$Date}','{$TimeDelta}','Пользователь {$Login} оставил комментарий к записи [Id: {$IdPost}]: {$Message}' )";
	$mysqli->query($Sql);


$query = $mysqli->prepare("INSERT INTO `comments`(`IdUser`, `IdPost`, `Messages`) VALUES (?, ?, ?)");
$query->bind_param("iis", $IdUser, $IdPost, $Message);

if ($query->execute()) {
    echo "Сообщение отправлено";
} else {
    echo "Ошибка при отправке";
}

$query->close();
?>
