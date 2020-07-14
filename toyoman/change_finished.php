<?php
	session_start();
	if(!isset($_SESSION['id'])){
		header('Location: login.php');
		exit;
	}
?>

<!DOCTYPE html>
<html>
	<?php require '../template/shift_head.php'; ?>

	<body>
		<header>
			<h1 id="title">豊萬シフト共有・管理</h1>
			<link rel="stylesheet" href="home.css">
			<h5 class="login_now">ログイン中：<?php echo $_SESSION['name'] ?>さん</h5>
		</header>
		<div id="pass_change">
			<div id="pass_changed">
				パスワードを変更しました
			</div>
		</div>

		<?php require '../template/shift_footer.php'; ?>
	</body>
</html>