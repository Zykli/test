<?php
	session_start();
	$pdo = new PDO("mysql:host=localhost;dbname=zenkin;charset=utf8", "zenkin", "neto0677", [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	]);

	$error = visible('none');

	function visible($z){
		return 'display:'.$z;
	}

	if(!empty($_POST['login'])) {
		$userName = filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING);
		$userPass = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
		$userLogin = "SELECT * FROM usersdiplom";
		$statement = $pdo->prepare($userLogin);
		$statement->execute();
		foreach ($statement as $row) {
			if($row['user'] == $userName && $row['password'] == $userPass) {
				$_SESSION['user'] = $row;
				header('Location: http://university.netology.ru/u/zenkin/test/admin.php');
				die;
			} else {
				$error = visible('block');
				// echo "Данного пользователя не существует. Зарегистрируйтесь!";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Авторизация</title>
		<style>
			h3 {
				margin: 0;
				padding: 0 0 15px 0;
			}
			form {
			    position: absolute;
			    top: 50%;
			    left: 50%;
			    width: 500px;
			    text-align: center;
			    padding: 15px;
			    background: #a4df85;
			    min-height: 115px;
			    margin-top: -70px;
    			margin-left: -250px;
			}
			label {
				width: 110px;
				display: inline-block;
			}
			#user, #password {
				margin-bottom: 10px;
			}
		</style>
	</head>
	<body>
		<form method="POST">
			<h3>Авторизация</h3>
			<label for="user">Пользователь:</label>
			<input id="user" name="user" value="" type="text">
			<br />
			<label for="password">Пароль:</label>
			<input id="password" name="password" type="password">
			<br />
			<div style="color: red; margin-bottom: 10px;<?= $error ?>">Пользователь или пароль неверен. <br> Либо данного пользователя не существует</div>
			<input type="submit" name="login" value="Войти">
			<a href="list.php">Вернуться к списку</a>
		</form>
	</body>
</html>