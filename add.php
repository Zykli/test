<?php
	$pdo = new PDO("mysql:host=localhost;dbname=zenkin;charset=utf8", "zenkin", "neto0677", [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	]);

	if(!empty($_POST['registerQuestion'])) {
		$wordign = filter_input(INPUT_POST, "wordign", FILTER_SANITIZE_STRING);
		$author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING);
		$categoryId = filter_input(INPUT_POST, "categoryId", FILTER_SANITIZE_NUMBER_INT);
		$createDate = date("Y-m-d H:m:s");
		$userInsert = "INSERT INTO question (wordign, author, category_id, create_date) VALUES (:wordign, :author, :category_id, :create_date)";
		$statement = $pdo->prepare($userInsert);
		$statement->execute(["wordign" => "$wordign", "author" => "$author", "category_id" => "$categoryId", "create_date" => "$createDate"]);
		echo "Вопрос успешно добавлен";
	}

	$sqlCategory = "SELECT * FROM category";
	$categoryList = $pdo->prepare($sqlCategory);
	$categoryList->execute();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Задать вопрос</title>
	</head>
	<body>
		<h4>Введите данные для регистрации или войдите, если уже регистрировались:</h4>
		<form method="POST">
			<label for="author">введите Ваше имя:</label>
			<input id="author" name="author" type="text">
			<br />
			<label for="wordign">введите вопрос:</label>
			<input id="wordign" name="wordign" value="" type="text">
			<br />
			<label for="categoryId">выберите категорию:</label>
			<select  name="categoryId">
				<?php foreach ($categoryList as $elem) :?>
					<option value="<?= $elem['id'] ?>"><?= $elem['name'] ?></option>
				<?php endforeach ?>
			</select>
			<br />
			<input type="submit" name="registerQuestion" value="добавить вопрос">
		</form>
		<a href="list.php">Вернуться к списку</a>
	</body>
</html>