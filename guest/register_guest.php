<?php
	session_start();
	if(!isset($_SESSION['id'])){
		header('Location: ../toyoman/login.php');
		exit;
	}
?>

<!DOCTYPE html>
<html>
	<?php require '../template/shift_head.php'; ?>
	
	<body>
		<header>
			<mata charset="UTF-8">
			<meta name="viewport" content="width=device-width,initial-scale=1">
			<link rel="stylesheet" href="home.css">
			<h1 id="title">豊萬シフト共有・管理</h1>
			<h5 class="login_now">ログイン中：<?php echo $_SESSION['name'] ?>さん</h5>
		</header>

		<h2 style="text-align: center;color: red;">ゲストモードでは従業員の編集はできません</h2>
		
		<?php require '../template/shift_footer_guest.php'; ?>
	</body>
</html>