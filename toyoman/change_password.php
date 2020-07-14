<?php

	session_start();
	if(!isset($_SESSION['id'])){
		header('Location: login.php');
		exit;
	}
	require_once('../static/sec/mysql_info.php');
	$error_message='';
	$error_num=0;
	if(isset($_POST['password']) && isset($_POST['re_password'])){
		if(!empty($_POST['password']) && !empty($_POST['re_password'])){
			if($_POST['password'] === $_POST['re_password']){
				if(4 <= strlen($_POST['password'])){
					if(strlen($_POST['password']) <= 12){
						try{
							$hash_pass=password_hash($_POST['password'],PASSWORD_DEFAULT);
							$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
							$sql='UPDATE user_data SET pass="'.$hash_pass.'" WHERE id='.$_SESSION['id'];
							$pdo->query($sql);
						}
						catch(PDOException $e){
							echo '接続失敗:'.$e;
						}
						finally{
							$pdo=null;
						}
						header('Location: change_finished.php');
						exit;
					}
					else{
						$error_num=4;
					}
				}
				else{
					$error_num=3;
				}
			}
			else{
				$error_num=2;
			}
		}
		else{
			$error_num=1;
		}
		switch($error_num){
			case 1:
				$error_message='入力されていない項目があります';
				break;
			case 2:
				$error_message='パスワードと確認用パスワードが一致しません';
				break;
			case 3:
				$error_message='パスワードの文字数が少なすぎます';
				break;
			case 4:
				$error_message='パスワードの文字数が多すぎます';
				break;
		}

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
		<?php
			if(!empty($error_message)) print '<p>'.$error_message.'</p>';
		?>
		<form method="post" action="change_password.php">
			<div id="change_form">
				<div>
					新しいパスワード　:
					<input type="password" name="password" placeholder="4文字～12文字の英数字">
				</div>
				<div>
					確認用パスワード　:
					<input type="password" name="re_password" placeholder="4文字～12文字の英数字">
				</div>
				<div id="change_button">
					<input type="submit" value="変更">
				</div>
			</div>
		</form>

		<?php require '../template/shift_footer.php'; ?>
	</body>
</html>