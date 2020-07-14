<?php
	session_start();
	if(!isset($_SESSION['id'])){
		header('Location: ../toyoman/login.php');
		exit;
	}
	unset($_SESSION['username']);
?>

<!DOCTYPE html>
<html>
	<?php require '../template/shift_head.php'; ?>

	<body>
		<header>
			<h1 id="title">豊萬シフト共有・管理</h1>
			<div id="menu">
		</header>
		<div id="logout">
			<div id="logout_msg">ログアウトしました</div>
			<a href="../toyoman/login.php">ログイン画面に戻る</a>
		</div>
	</body>
</html>