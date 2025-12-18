<?php
	session_start();
	include("./settings/connect_datebase.php");
	include("./settings/session.php");
	// $CSRF = password_hash(password: "SECRET", algo: PASSWORD_DEFAULT);
	// $_SESSION["CSRF"] = $CSRF;
	// define('ENCRYPTION_KEY', 'your-32-char-secret-key-here!123');
	// function generateEncryptedToken() {
	// 	if (!isset($_SESSION['user'])) {
	// 		return null;
	// 	}
		
	// 	$tokenData = [
	// 		'user_id' => $_SESSION['user'],
	// 		'timestamp' => time(),
	// 		'session_id' => session_id()
	// 	];
		
	// 	$iv = openssl_random_pseudo_bytes(16);
	// 	$encrypted = openssl_encrypt(
	// 		json_encode($tokenData),
	// 		'AES-256-CBC',
	// 		ENCRYPTION_KEY,
	// 		0,
	// 		$iv
	// 	);
    
    // 	return base64_encode($iv . $encrypted);
	// }
	// $encryptedToken = generateEncryptedToken();
?>
<!DOCTYPE HTML>
<html>
	<head> 
		<meta charset="utf-8">
		<title> WEB-безопасность </title>
		
		<link rel="stylesheet" href="style.css">
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
	</head>
	<body>
		<div class="top-menu">
			<a class="button" href = "./login.php">Войти</a>
		
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЕЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
			
			
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class="name">Новости:</div>
				
				<div>
					<?php
						$query_news = $mysqli->query("SELECT * FROM `news`;");
						while($read_news = $query_news->fetch_assoc()) {
							$QueryMessages = $mysqli->query("SELECT * FROM `comments` WHERE `IdPost` = {$read_news["id"]}");

							echo '
								<div class="specialty">
									<div class = "slider">
										<div class = "inner">
											<div class="name">'.htmlspecialchars($read_news["title"]).'</div>
											<div class="description" style="overflow: auto;">
												<img src = "'.htmlspecialchars($read_news["img"]).'" style="width: 50px; box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.12), 0 1px 5px 0 rgba(0,0,0,.2); float: left; margin-right: 10px;">
												'.htmlspecialchars($read_news["text"]).'
												
											</div>
											<div class="messages">
												';
												while($ReadMessages = $QueryMessages->fetch_assoc()) {
													echo "<div>".htmlspecialchars($ReadMessages["Messages"])."</div>";
												}
											echo '</div>';

											
											if (isset($_SESSION['user'])) {
												echo 
													'<div class="messages" id="'.htmlspecialchars($read_news["id"]).'">
														<input type="text">
													 
														<div class="button" style="float: right; margin-top: 0px; margin-right: 0px;" onclick="SendMessage(this)">Отправить</div>
													</div>';
											}
											//  <input type="text" value="'.$CSRF.'" style="display: none;"> 
										echo 
										'</div>
									</div>
								</div>
							';
						}
					?>
					<div class="footer">
						© КГАПОУ "Авиатехникум", 2020
						<a href=#>Конфиденциальность</a>
						<a href=#>Условия</a>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script>
		function SendMessage(sender) {
			let Message = sender.parentElement.children[0].value;
			// let CSRF = sender.parentElement.children[1].value;
			let IdPost = sender.parentElement.id;
			if(Message == "") return;

			var Data = new FormData();
			Data.append("Message", Message);
			Data.append("IdPost", IdPost);
			//Data.append("CSRF", CSRF);
			// Data.append("encrypted_token", encryptedToken); 
			$.ajax({
					url         : 'ajax/message.php',
					type        : 'POST',
					data        : Data,
					cache       : false,
					dataType    : 'html',
					processData : false,
					contentType : false, 
					success: function (_data) {
						console.log(_data);
						sender.parentElement.children[0].value = "";
						let safeMessage = Message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
						sender.parentElement.parentElement.children[2].innerHTML += "<div>" + safeMessage + "</div>";
					},
					// функция ошибки
					error: function( ){
						console.log('Системная ошибка!');
					}
				});
		}
	</script>
</html>
