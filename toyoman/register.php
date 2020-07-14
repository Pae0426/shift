<?php
	session_start();
	if(!isset($_SESSION['id'])){
		header('Location: login.php');
		exit;
	}
	require_once('../static/sec/mysql_info.php');
	/*$error_message='';
	$error_num=0;
	if(isset($_POST['name']) && isset($_POST['password']) && isset($_POST['re_password'])){
		if(!empty($_POST['name']) && !empty($_POST['password']) && !empty($_POST['re_password'])){
			if($_POST['password'] === $_POST['re_password']){
				if(4 <= strlen($_POST['password'])){
					if(strlen($_POST['password']) <= 12){
						try{
							$hash_pass=password_hash($_POST['password'],PASSWORD_DEFAULT);
							$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
							$sql=$pdo->prepare('INSERT INTO user_data values(?,?,?)');
							$sql->execute([$_POST['employee_num'],$_POST['name'],$hash_pass]);
						}
						catch(PDOException $e){
							echo '接続失敗:'.$e;
						}
						finally{
							$pdo=null;
						}
						header('Location: register_finished.php');
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

	}*/
?>

<!DOCTYPE html>
<html>
	<?php require '../template/shift_head.php'; ?>

	<body>
		<header>
			<h1 id="title">豊萬シフト共有・管理</h1>
		</header>
		<?php
			//if(!empty($error_message)) echo '<p>'.$error_message.'</p>';
		?>
		<!--<form method="post" action="register.php">
			<div id="register">
				<div id="register_form">
					<div>
						従業員番号　　　　:
						<input type="text" name="employee_num" size="20">
					</div>
					<div>
						氏名　　　　　　　:
						<input type="text" name="name" size="20" placeholder="フルネームで入力">
					</div>
					<div>
						パスワード　　　　:
						<input type="password" name="password" placeholder="4～12文字で入力">
					</div>
					<div>
						確認用パスワード　:
						<input type="password" name="re_password" placeholder="4～12文字で入力">
					</div>
				</div>
				<div id="register_button">
					<input type="submit" value="登録">
				</div>-
				<a href="login.php">ログイン画面に戻る</a>
			</div>
		</form>-->

		<h2 style="text-align: center;color: red">ただいま製作中です</h2>

		<?php require '../template/shift_footer.php'; ?>
	</body>
</html>