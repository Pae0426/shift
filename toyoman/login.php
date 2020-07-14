<?php
	session_start();
	$error_message='';
	$error_num=0;
	require_once('../static/sec/mysql_info.php');

	if(isset($_POST['employee_num']) && isset($_POST['password'])){
		if(!empty($_POST['employee_num']) && !empty($_POST['password'])){
			//ゲスト
			if($_POST['employee_num']==999 && $_POST['password']==="guest"){
				$_SESSION['id']=5;
				$_SESSION['name']='ゲスト';
				header('Location: ../guest/home_guest.php');
				exit;
			}
			//ゲスト(店長)
			if($_POST['employee_num']==9999 && $_POST['password']==="guest"){
				$_SESSION['id']=1;
				$_SESSION['name']='ゲスト(店長)';
				header('Location: ../guest/home_guest.php');
				exit;
			}

			//従業員
			try{
				$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
				$sql=$pdo->prepare('SELECT * FROM user_data WHERE id=?');
				$sql->execute([$_POST['employee_num']]);
				foreach($sql as $row){
					if(password_verify($_POST['password'],$row['pass'])){
						$_SESSION['id']=$row['id'];
						$_SESSION['name']=$row['name'];
						header('Location: home.php');
						exit;
					}
					else{
						$error_num=2;
					}
				}
			}
			catch(PDOException $e){
				echo '接続失敗:'.$e;
			}
			finally{
				$pdo=null;
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
				$error_message='ログイン名またはパスワードが違います';
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
			<div id="menu">
		</header>
		<?php
			if(!empty($error_message)) print '<p>'.$error_message.'</p>';
		?>
		<form method="post" action="./login.php" id="login_info">
			<div id="input_id">
				従業員番号　:
				<input type="number" name="employee_num" size="20" />
			
			</div>
			<div id="input_pass">
				パスワード　:
				<input type="password" name="password" size="20" />
			</div>
			<div id="login_button">
				<input type="submit" value="ログイン" />
			</div>
			<br>
		</form>
	</body>
</html>