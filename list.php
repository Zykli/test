<?php
	session_start();

	

	$pdo = new PDO("mysql:host=localhost;dbname=zenkin;charset=utf8", "zenkin", "neto0677", [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	]);

	$question = "SELECT * FROM question WHERE status = 1";
	$questionStatement = $pdo->prepare($question);
	$questionStatement->execute();

	$category = "SELECT * FROM category";
	$categoryStatement = $pdo->prepare($category);
	$categoryStatement->execute();

	$categoryMass = $categoryStatement->fetchAll();
	$questionMass = $questionStatement->fetchAll();

	$notNullCategories = [];
	foreach ($questionMass as $rowquestion) {
		if(!(in_array($rowquestion["category_id"], $notNullCategories))) {
			array_push($notNullCategories, $rowquestion["category_id"]);
		}
	}

	for ($z=0; $z < count($notNullCategories) ; $z++) { 
		// echo $notNullCategories[$z];
		// 	echo '<br>';
	}
	$categoryMassForView = [];
	foreach ($categoryMass as $elem) {
		if (in_array($elem['id'], $notNullCategories)) {
			array_push($categoryMassForView, $elem);
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<style type="text/css">
			body {
				margin: 0;
				padding: 0;
			}
			.header {
				text-align: center;
				/*font-size: 24px;*/
				background-color: #a4df85;
				padding: 150px 0px ;
				margin: 0;
			}
			h4 {
				margin: 0;
			}
		</style>
		<script type="text/javascript">
			window.onload = function () {
				var question = document.getElementsByClassName('wordign');
				for (var i = 0; i < question.length; i++) {
					question[i].onclick = function() {
						var nextElement = this.nextSibling.nextSibling
						var display = nextElement.style.display;
						if (display === "none") {
							nextElement.style.display = "block";
						} else {
							nextElement.style.display = "none";
						}
					}
				}
				
			}
		</script>
	</head>
	<body>
		<div>
			<div style="width: 800px; margin: 0 auto; position: relative;">
				<div style="position: absolute; top: 0; right: 0;">
					<a href="login.php">Войти</a>
				</div>
			</div>
			<h2 class="header">FAQ</h2>
		</div>
		
		<div style="width: 800px; margin: 0 auto;">
			<div style="float: left">
				<div>
					<a href="add.php">Задать вопрос</a>
				</div>
				<div>
					<h4>Категории</h4>
				</div>
				<?php foreach ($categoryMassForView as $rowcategory): ?>
					<div>
						<a href="#<?=$rowcategory["name"]?>"><?= htmlspecialchars($rowcategory["name"])?></a>
					</div>
				<?php endforeach ?>
			</div>
			<div style="margin-left: 100px;">
				<ul style="list-style: none;">
					<?php foreach ($categoryMassForView as $rowcategory): ?>
					<li id="<?=$rowcategory["name"]?>" class="category-head"><?= htmlspecialchars($rowcategory["name"])?></li>
						<?php foreach ($questionMass as $rowquestion): ?>
							<?php if($rowcategory["id"] == $rowquestion["category_id"] && $rowquestion["answer"]): ?>
							<li style="border: 1px solid;">
								<a class="wordign" href="#<?= $rowquestion["id"]?>" style="display: block; width: 600px; word-break: break-word;"><?= htmlspecialchars($rowquestion["wordign"])?></a>
								<div id="<?= $rowquestion["id"]?>" style="display: none; width: 600px; word-break: break-word;"><?= htmlspecialchars($rowquestion["answer"])?></div>
							</li>
							<?php endif ?>
						<?php endforeach ?>
					<?php endforeach ?>
				</ul>
			</div>

		</div>
	</body>
</html>