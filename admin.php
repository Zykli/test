<?php
	session_start();

	$pdo = new PDO("mysql:host=localhost;dbname=zenkin;charset=utf8", "zenkin", "neto0677", [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	]);

	function isLogged() {
	  return !empty($_SESSION['user']);
	}

	$adminData = visible('none');
	$adminInsert = visible('none');
	$errorDeleteAdmin = visible('none');
	$adminChange = visible('none');
	$themeData = visible('none');
	$themeInsert = visible('none');
	$questionThemeData = visible('none');
	$changeCategory = visible('none');
	$noAnsverQuestions = visible('none');
	$editQuestion = visible('none');
	$editNoAnswerQuestion = visible('none');
	
	$themeName = '';
	$editWording = '';
	$editAnswer = '';

	function visible($z){
		return 'display:'.$z;
	}

	if(!isLogged()) {
		header('Location: http://university.netology.ru/u/zenkin/test/login.php');
    	exit();
	}

	if (isset($_POST['changeQuestion'])) {
		$newWodring = filter_input(INPUT_POST, "editWording", FILTER_SANITIZE_STRING);
		$newAnswer = filter_input(INPUT_POST, "editAnswer", FILTER_SANITIZE_STRING);
		$newAuthor = filter_input(INPUT_POST, "editAuthor", FILTER_SANITIZE_STRING);
		$questionId = filter_input(INPUT_GET, "edit", FILTER_SANITIZE_NUMBER_INT);
		if (isset($_POST['publicate'])) {
			$newStatus = 1;
		} else {
			$newStatus = 0;
		}
		$changeQuestion = "UPDATE question SET wordign = :newWodring, answer = :newAnswer, status = :newStatus, author = :newAuthor WHERE id = :questionId";
		$statement = $pdo->prepare($changeQuestion);
		$statement->execute(["questionId" => "$questionId", "newWodring" => "$newWodring", "newAnswer" => "$newAnswer", "newStatus" => "$newStatus", "newAuthor" => "$newAuthor"]);
		$changeQuestion = visible('none');
	}
	if (isset($_POST['changeNoAnswerQuestion'])) {
		$newWodring = filter_input(INPUT_POST, "editWording", FILTER_SANITIZE_STRING);
		$newAnswer = filter_input(INPUT_POST, "editAnswer", FILTER_SANITIZE_STRING);
		$categoryId = filter_input(INPUT_POST, "CategoryId", FILTER_SANITIZE_NUMBER_INT);
		$newAuthor = filter_input(INPUT_POST, "editAuthor", FILTER_SANITIZE_STRING);
		$questionId = filter_input(INPUT_GET, "editNoAnswerQuestion", FILTER_SANITIZE_NUMBER_INT);
		if (isset($_POST['publicate'])) {
			$newStatus = 1;
		} else {
			$newStatus = 0;
		}
		$changeQuestion = "UPDATE question SET wordign = :newWodring, answer = :newAnswer, status = :newStatus, category_id = :categoryId, author = :newAuthor WHERE id = :questionId";
		$statement = $pdo->prepare($changeQuestion);
		$statement->execute(["questionId" => "$questionId", "newWodring" => "$newWodring", "newAnswer" => "$newAnswer", "newStatus" => "$newStatus", "newAuthor" => "$newAuthor", "categoryId" => "$categoryId"]);
		$editNoAnswerQuestion = visible('none');
	}


	if (isset($_GET['action']) && $_GET['action']=='admins') {
		$adminData = visible('block');
		if (isset($_GET['admin']) && $_GET['admin']=='insert') {
			$adminInsert = visible('block');
		}
		if (isset($_GET['delete'])) {

			$adminId = filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_NUMBER_INT);
			if ($_SESSION['user']['id'] == $adminId) {
				$errorDeleteAdmin = visible('block');
			} else {
				$deleteAdmin = "DELETE FROM usersdiplom WHERE id = :adminId";
				$statement = $pdo->prepare($deleteAdmin);
				$statement->execute(["adminId" => "$adminId"]);
			}
		}
		if (isset($_GET['changeAdminPassword'])) {
			$adminChange = visible('block');
			$newPass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
			$userChandeId = filter_input(INPUT_GET, 'changeAdminPassword', FILTER_SANITIZE_NUMBER_INT);
			$changePass = "UPDATE usersdiplom SET password = :password WHERE id = :userid";
			$statement = $pdo->prepare($changePass);
			$statement->execute(["userid" => "$userChandeId", "password" => "$newPass"]);
		}
	}


	// темы
	if (isset($_GET['action']) && $_GET['action']=='theme') {
		$themeData = visible('block');
		if (isset($_GET['theme']) && $_GET['theme']=='insert') {
			$themeInsert = visible('block');
		}
		if (isset($_GET['question'])) {
			$noAnsverQuestions = visible('block');
			if (isset($_GET['deleteQuestion'])) {
				$questionId = filter_input(INPUT_GET, 'deleteQuestion', FILTER_SANITIZE_NUMBER_INT);
				$deleteQuestions = "DELETE FROM question WHERE id = :questionId";
				$statement = $pdo->prepare($deleteQuestions);
				$statement->execute(["questionId" => "$questionId"]);
			}
			if (isset($_GET['editNoAnswerQuestion'])) {
				$questionId = filter_input(INPUT_GET, 'editNoAnswerQuestion', FILTER_SANITIZE_NUMBER_INT);

				$questionEdit = "SELECT * FROM question WHERE id = :questionId";
				$questionEditStatement = $pdo->prepare($questionEdit);
				$questionEditStatement->execute(["questionId" => "$questionId"]);
				$questionEditMass = $questionEditStatement->fetchAll();

				$editWording = $questionEditMass[0]['wordign'];
				$editAnswer = $questionEditMass[0]['answer'];
				$editAuthor = $questionEditMass[0]['author'];
				$categoryChoozen = $questionEditMass[0]['category_id'];
				$editNoAnswerQuestion = visible('block');
			}
		}
		if (isset($_GET['delete'])) {
			$themeId = filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_NUMBER_INT);
			$deleteTheme = "DELETE FROM category WHERE id = :themeId";
			$statement = $pdo->prepare($deleteTheme);
			$statement->execute(["themeId" => "$themeId"]);
			$deleteAllQuestions = "DELETE FROM question WHERE category_id = :themeId";
			$statementAllQuestions = $pdo->prepare($deleteAllQuestions);
			$statementAllQuestions->execute(["themeId" => "$themeId"]);
		}
		if (isset($_GET['deleteAllQuestions'])) {
			$themeId = filter_input(INPUT_GET, 'deleteAllQuestions', FILTER_SANITIZE_NUMBER_INT);
			$deleteAllQuestions = "DELETE FROM question WHERE category_id = :themeId";
			$statement = $pdo->prepare($deleteAllQuestions);
			$statement->execute(["themeId" => "$themeId"]);
		}

		if (isset($_GET['themedata'])) {
			
			if (isset($_GET['publicate'])) {
				$questionId = filter_input(INPUT_GET, 'publicate', FILTER_SANITIZE_NUMBER_INT);
				$changeStatus = "UPDATE question SET status = 1 WHERE id = :questionId";
				$statement = $pdo->prepare($changeStatus);
				$statement->execute(["questionId" => "$questionId"]);
			}	
			if (isset($_GET['hide'])) {
				$questionId = filter_input(INPUT_GET, 'hide', FILTER_SANITIZE_NUMBER_INT);
				$changeStatus = "UPDATE question SET status = 0 WHERE id = :questionId";
				$statement = $pdo->prepare($changeStatus);
				$statement->execute(["questionId" => "$questionId"]);
			}	
			if (isset($_GET['deleteQuestion'])) {
				$questionId = filter_input(INPUT_GET, 'deleteQuestion', FILTER_SANITIZE_NUMBER_INT);
				$deleteQuestions = "DELETE FROM question WHERE id = :questionId";
				$statement = $pdo->prepare($deleteQuestions);
				$statement->execute(["questionId" => "$questionId"]);
			}
			if (isset($_GET['edit'])) {
				$questionId = filter_input(INPUT_GET, 'edit', FILTER_SANITIZE_NUMBER_INT);
				
				$questionEdit = "SELECT * FROM question WHERE id = :questionId";
				$questionEditStatement = $pdo->prepare($questionEdit);
				$questionEditStatement->execute(["questionId" => "$questionId"]);
				$questionEditMass = $questionEditStatement->fetchAll();

				$editWording = $questionEditMass[0]['wordign'];
				$editAnswer = $questionEditMass[0]['answer'];
				$editAuthor = $questionEditMass[0]['author'];
				$editQuestion = visible('block');
			}
			if (isset($_GET['changeCategory'])) {
				$changeCategory = visible('block');
			}

			$questionThemeData = visible('block');
			$themeId = filter_input(INPUT_GET, 'themedata', FILTER_SANITIZE_NUMBER_INT);

			$themeNameSql = "SELECT name FROM category WHERE id = :themeId";
			$themeNameStatement = $pdo->prepare($themeNameSql);
			$themeNameStatement->execute(["themeId" => "$themeId"]);
			$themeNameMass = $themeNameStatement->fetchAll();
			$themeName = $themeNameMass[0]['name'];

			$oneCategoryDataSql = "SELECT * FROM question WHERE category_id = :themeId";
			$oneCategoryDataStatement = $pdo->prepare($oneCategoryDataSql);
			$oneCategoryDataStatement->execute(["themeId" => "$themeId"]);
		}
	}

	if (isset($_POST['createAdmin'])) {
		$userName = filter_input(INPUT_POST, "user", FILTER_SANITIZE_STRING);
		$userPass = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
		$createAdmin = "INSERT INTO usersdiplom (user, password) VALUES (:user, :password)";
		$statement = $pdo->prepare($createAdmin);
		$statement->execute(["user" => "$userName", "password" => "$userPass"]);
		$adminInsert = visible('none');
	}

	if (isset($_POST['createTheme'])) {
		$themeName = filter_input(INPUT_POST, "newTheme", FILTER_SANITIZE_STRING);
		$createAdmin = "INSERT INTO category (name) VALUES (:name)";
		$statement = $pdo->prepare($createAdmin);
		$statement->execute(["name" => "$themeName"]);
		$themeInsert = visible('none');
	}

	if (isset($_POST['changeCategory'])) {
		$categoryId = filter_input(INPUT_POST, "newCategoryId", FILTER_SANITIZE_NUMBER_INT);
		$questionId = filter_input(INPUT_GET, "changeCategory", FILTER_SANITIZE_NUMBER_INT);
		$changeCategory = "UPDATE question SET category_id = :newCategory WHERE id = :questionId";
		$statement = $pdo->prepare($changeCategory);
		$statement->execute(["questionId" => "$questionId", "newCategory" => "$categoryId"]);
	}

	

	

	

	
	$pdo = new PDO("mysql:host=localhost;dbname=zenkin;charset=utf8", "zenkin", "neto0677", [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	]);

	$sqlUsers = "SELECT * FROM usersdiplom";
	$userList = $pdo->prepare($sqlUsers);
	$userList->execute();

	$sqlCategory = "SELECT * FROM category";
	$categoryList = $pdo->prepare($sqlCategory);
	$categoryList->execute();
	$categoryMass = $categoryList->fetchAll();

	$question = "SELECT * FROM question";
	$questionStatement = $pdo->prepare($question);
	$questionStatement->execute();
	$questionMass = $questionStatement->fetchAll();


	$assocMassThemeQuestions=[];
	foreach ($categoryMass as $elemCategory) {
		$assocMassThemeQuestions[$elemCategory['name']] = [];
		$allQuestionsCategory = 0;
		$visibleQuestionsCategory = 0;
		$noAnswerQuestionsCategory = 0;
		foreach ($questionMass as $elemQuestion) {
			if ($elemCategory['id'] == $elemQuestion['category_id']) {
				$allQuestionsCategory++;
				if (!$elemQuestion['answer']) {
					$noAnswerQuestionsCategory++;
				}
				if ($elemQuestion['status'] == 1) {
					$visibleQuestionsCategory++;
				}
			}
		}
		array_push($assocMassThemeQuestions[$elemCategory['name']], $allQuestionsCategory);
		array_push($assocMassThemeQuestions[$elemCategory['name']], $visibleQuestionsCategory);
		array_push($assocMassThemeQuestions[$elemCategory['name']], $noAnswerQuestionsCategory);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Администрирование</title>
		<style>
			body {
				position: relative;
				padding: 0;
				margin: 0;
			}
			.header {
				text-align: center;
				background-color: #a4df85;
				padding: 150px 0px ;
				margin: 0;
			}
			.close {
				position:absolute;
				right: 10px;
    			top: 10px;
    			height: 13px;
    			width: 13px;
			}
			.close::after, .close::before {
				content: '';
				width: 13px;
			    border: 1px solid black;
			    transform: rotate(45deg);
			    position: absolute;
			    top: 5px;
			    left: 0;
			}
			.close:hover::after, .close:hover::before {
				border-color: blue;
			}
			.close::before {
			    transform: rotate(135deg);
			}
			form {
				background-color: beige;
				text-align: center;
				padding: 10px 0;
			}
			label {
				width: 110px;
				display: inline-block;
			}
			#user, #password {
				margin-bottom: 10px;
			}
			table {
				border-collapse: collapse;
				margin: 20px auto 0;
			}
			td, th {
				border: 1px solid black;
				padding: 2px 4px;
			}
			th {
				background-color: grey;
			}
			td > a {
				margin-right: 5px;
			}
		</style>
	</head>
	<body style="position: relative;">
		<div>
			<h2 class="header">Администрирование</h2>
		</div>
		<div style="width: 800px; margin: 0 auto;">
			<div style="float: left">
				<a href="?action=admins">Администраторы</a><br>
				<a href="?action=theme">Темы</a><br>
		<a href="logout.php">выйти</a>
			</div>

			<!-- администраторы -->
			<div style="margin-left: 150px; position: relative;
    background-color: beige; padding: 10px;<?= $adminData ?>">
				<h3 style="text-align: center; margin: 0; padding: 0">Администраторы</h3>
				<a href="admin.php" class="close"></a>
				<div></div>
				<a href="?action=admins&admin=insert">Создать</a>
				<form style="<?= $adminInsert ?>;" method="POST">
					<label for="user">Имя:</label>
					<input id="user" name="user" value="" type="text">
					<br />
					<label for="password">Пароль:</label>
					<input id="password" name="password" type="password">
					<br />
					<input type="submit" name="createAdmin" value="Создать">
				</form>
				<form style="<?= $adminChange ?>;" method="POST">
					<label for="password">Новый пароль:</label>
					<input id="password" name="password" type="password">
					<br />
					<input type="submit" name="changeAdminPassword" value="Изменить">
				</form>
				<div style="background-color: red; <?= $errorDeleteAdmin ?>">Нельзя удалить текущего авторизованного пользователя</div>
				<table>
					<thead>
						<tr>
							<th>Имя пользователя</th>
							<th>Действия</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($userList as $row):?>
						<tr>
							<td><?= $row['user'] ?> </td>
							<td>
								<a href="?action=admins&delete=<?= $row['id'] ?>">Удалить</a>
								<a href="?action=admins&changeAdminPassword=<?= $row['id'] ?>">Изменить пароль</a>
							</td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<!-- темы -->
			<div style="margin-left: 150px; position: relative;
    background-color: beige; padding: 10px;<?= $themeData ?>">
				<h3 style="text-align: center; margin: 0; padding: 0">Темы</h3>
				<a href="admin.php" class="close"></a>
				<div></div>
				<a href="?action=theme&theme=insert">Создать</a>
				<a href="?action=theme&question=noAnsver">Список вопросов без ответа</a>
				<form style="<?= $themeInsert ?>;" method="POST">
					<label for="newTheme">Имя:</label>
					<input id="newTheme" name="newTheme" value="" type="text">
					<br />
					<input type="submit" name="createTheme" value="Создать">
				</form>
				<table>
					<thead>
						<tr>
							<th>Имя темы</th>
							<th>Вопросов всего</th>
							<th>Вопросов опубликовано</th>
							<th>Вопросов без ответов</th>
							<th>Действия</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($categoryMass as $row):?>
						<tr>
							<td><a href="?action=theme&themedata=<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
							<td><?= $assocMassThemeQuestions[$row['name']][0] ?></td>
							<td><?= $assocMassThemeQuestions[$row['name']][1] ?></td>
							<td><?= $assocMassThemeQuestions[$row['name']][2] ?></td>
							<td>
								<a href="?action=theme&delete=<?= $row['id'] ?>">Удалить</a><br>
								<a href="?action=theme&deleteAllQuestions=<?= $row['id'] ?>">Удалить все вопросы</a>
							</td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<!-- вопросы без ответов -->
			<div style="position: absolute; top: 0;	left: 0; height: 100vh; width: 100vw; background-color: rgba(0,0,0,.5);<?= $noAnsverQuestions ?>">
				<div style="width: 80%; margin-top: 4vw; margin-left: -42%; position: absolute; top: 0; left: 50%; border: 1px solid; background-color: beige; padding: 10px; overflow-x: auto; max-height: 80%;">
					<h3 style="text-align: center; margin: 0; padding: 0">Все вопросы без ответов</h3>
					<a href="?action=theme" class="close"></a>
					<div></div>
					<form style="<?= $editNoAnswerQuestion ?>;" method="POST">
						<label style="vertical-align: top;" for="editWording">Вопрос:</label>
						<textarea style="width: 50%;" id="editWording" name="editWording" type="text"><?= $editWording ?></textarea>
						<br />
						<label style="vertical-align: top;" for="editAnswer">Ответ:</label>
						<textarea style="width: 50%;" id="editAnswer" name="editAnswer" type="text"><?= $editAnswer ?></textarea>
						<br />
						<label style="vertical-align: top;" for="editAuthor">Автор:</label>
						<input style="width: 50%;" id="editAuthor" name="editAuthor" type="text" value="<?= $editAuthor ?>">
						<br />
						<input style="margin-bottom: 10px;" type="checkbox" id="publicate" name="publicate">
						<label for="publicate">Опубликовать</label>
						<br />
						<select id="newCategoryId" name="CategoryId">
							<?php foreach ($categoryMass as $elem) :?>
								<option value="<?= $elem['id'] ?>" <?= $elem['id'] == $categoryChoozen ? 'selected' : '' ?>><?= $elem['name'] ?></option>
							<?php endforeach ?>
						</select>
						<br />
						<input type="submit" name="changeNoAnswerQuestion" value="Записать">
					</form>
					<table style="width: 100%;">
						<thead>
							<tr>
								<th>Вопрос</th>
								<th>Ответ</th>
								<th>Дата добавления</th>
								<th>Автор</th>
								<th>Тема</th>
								<th>Статус</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($questionMass as $row):?>
							<?php if ($row['answer'] == null):?>
							<tr>
								<td style="word-break: break-all; width: 35%;"><?= $row['wordign'] ?></td>
								<td style="word-break: break-all; width: 35%;"><?= $row['answer'] ?></td>
								<td><?= htmlspecialchars($row["create_date"], ENT_QUOTES) ?></td>
								<td><?= htmlspecialchars($row["author"], ENT_QUOTES) ?></td>
								<?php foreach ($categoryMass as $elem) :?>
								<?php if ($elem['id'] == $row["category_id"]) :?>
									<td>
										<?= htmlspecialchars($elem["name"], ENT_QUOTES) ?>
									</td>
								<?php endif ?>
								<?php endforeach ?>
								
								<?php if($row['answer'] == null) {
										echo '<td style="color: orange;">Ожидает ответа</td>';
									} else {
										if($row['status'] == 1) {
											echo '<td style="color: green;">Опубликован</td>';
										} else {
											echo '<td style="color: red;">Cкрыт</td>';
										}
									} ?>
								
								<td>
									<a href="?action=theme&question=noAnsver&deleteQuestion=<?= $row['id'] ?>">Удалить</a><br>
									<a href="?action=theme&question=noAnsver&editNoAnswerQuestion=<?= $row['id'] ?>">Изменить</a><br>
								</td>
							</tr>
							<?php endif ?>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- вопросы темы -->
			<div style="position: absolute; top: 0;	left: 0; height: 100vh; width: 100vw; background-color: rgba(0,0,0,.5);<?= $questionThemeData ?>">
				<div style="width: 80%; margin-top: 4vw; margin-left: -42%; position: absolute; top: 0; left: 50%; border: 1px solid; background-color: beige; padding: 10px; overflow-x: auto; max-height: 80%;">
					<h3 style="text-align: center; margin: 0; padding: 0">Вопросы по теме <?= $themeName ?></h3>
					<a href="?action=theme" class="close"></a>
					<div></div>
					<form style="<?= $editQuestion ?>;" method="POST">
						<label style="vertical-align: top;" for="editWording">Вопрос:</label>
						<textarea style="width: 50%;" id="editWording" name="editWording" type="text"><?= $editWording ?></textarea>
						<br />
						<label style="vertical-align: top;" for="editAnswer">Ответ:</label>
						<textarea style="width: 50%;" id="editAnswer" name="editAnswer" type="text"><?= $editAnswer ?></textarea>
						<br />
						<label style="vertical-align: top;" for="editAuthor">Автор:</label>
						<input style="width: 50%;" id="editAuthor" name="editAuthor" type="text" value="<?= $editAuthor ?>">
						<br />
						<input style="margin-bottom: 10px;" type="checkbox" id="publicate" name="publicate">
						<label for="publicate">Опубликовать</label>
						<br />
						<input type="submit" name="changeQuestion" value="Записать">
					</form>
					<table style="width: 100%;">
						<thead>
							<tr>
								<th>Вопрос</th>
								<th>Ответ</th>
								<th>Дата добавления</th>
								<th>Автор</th>
								<th>Статус</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($oneCategoryDataStatement as $row):?>
							<tr>
								<td style="word-break: break-all; width: 35%;"><?= $row['wordign'] ?></td>
								<td style="word-break: break-all; width: 35%;"><?= $row['answer'] ?></td>
								<td><?= htmlspecialchars($row["create_date"], ENT_QUOTES) ?></td>
								<td><?= htmlspecialchars($row["author"], ENT_QUOTES) ?></td>
								<?php if($row['answer'] == null) {
										echo '<td style="color: orange;">Ожидает ответа</td>';
									} else {
										if ($row['status'] == 1) {
											echo '<td style="color: green;">Опубликован</td>';
										} else if ($row['status'] == 0) {
											echo '<td style="color: red;">Cкрыт</td>';
										}
									} ?>
								<td>
									<a href="?action=theme&themedata=<?= $themeId ?>&deleteQuestion=<?= $row['id'] ?>">Удалить</a><br>
									<a href="?action=theme&themedata=<?= $themeId ?>&edit=<?= $row['id'] ?>">Изменить</a><br>
									<?php if ($row['status'] == 0):?>
									<a href="?action=theme&themedata=<?= $themeId ?>&publicate=<?= $row['id'] ?>">Опубликовать</a><br>
									<?php else :?>
									<a href="?action=theme&themedata=<?= $themeId ?>&hide=<?= $row['id'] ?>">Скрыть</a><br>
									<?php endif ?>
									<a href="?action=theme&themedata=<?= $themeId ?>&changeCategory=<?= $row['id'] ?>">Изменить тему</a><br>
								</td>
							</tr>
							<?php endforeach ?>
						</tbody>
					</table>

					
				</div>
			</div>

			<div style="position: absolute; top: 0; left: 0; height: 100vh; width: 100vw; background-color: rgba(0,0,0,.5); width: 100vw; <?= $changeCategory ?>">
				<form method="POST" style="width: 500px; padding: 15px 0; margin: 13% auto 0; position: relative;">
					<label style="width: 200px;" for="newCategoryId">Выберите новую тему:</label>
					<select id="newCategoryId" name="newCategoryId">
						<?php foreach ($categoryMass as $elem) :?>
							<option value="<?= $elem['id'] ?>"><?= $elem['name'] ?></option>
						<?php endforeach ?>
					</select>
					<input type="submit" name="changeCategory" value="Изменить">
					<a href="?action=theme&themedata=<?= $themeId ?>" class="close"></a>
				</form>
			<div>
		</div>
	</body>
</html>