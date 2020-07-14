<?php
	session_start();
	if(!isset($_SESSION['id'])){
		header('Location: login.php');
		exit;
	}
	unset($_SESSION['username']);
?>

<!DOCTYPE html>
<html>
	<?php require '../template/shift_head.php'; ?>

	<header>
		<h1 id="title">豊萬シフト共有・管理</h1>
		<div id="menu">
	</header>
	<head>
		<mata charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title>豊萬シフト共有</title>
		<link rel="stylesheet" href="home.css">
	</head>
	<body>
		<div id="logout">
			<div id="logout_msg">ログアウトしました</div>
			<a href="login.php">ログイン画面に戻る</a>
		</div>
	</body>
</html>