<?php
	session_start();
	if(!isset($_SESSION['id'])){
		header('Location: login.php');
		exit;
	}
	require_once('../static/sec/mysql_info.php');
	$line_array = ["\r\n", "\r", "\n"];
?>

<?php
	$year=date('Y');
	$month=date('n');
	$day=date('j');
	$days=date('t');
	$days_str='';
	$space_str='';
	$user_names=[];

	$week=['日','月','火','水','木','金','土'];
	$employee_count=0;

	$year_month;
	$prev_month=$month;
	if($month==12){
		$year+=1;
		$month=1;
		$year_month=$year.'-1';
	}
	else{
		$month+=1;
		$year_month=$year.'-'.$month;
	}
	$table_name='shift_month'.$month;
	$days=date('d',strtotime('last day of '.$year_month));

	for($i=1;$i<=$days;$i++){$days_str=$days_str.',day'.$i.' text';}
	for($i=1;$i<=$days;$i++){$space_str=$space_str.'," "';}


	try{
		$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
		
		#シフト表がなければ生成
		if(!($pdo->query('SELECT * FROM '.$table_name))){
			$user=$pdo->query('SELECT * FROM user_data');
			$pdo->query('CREATE TABLE IF NOT EXISTS '.$table_name.'(id int,FOREIGN KEY(id) REFERENCES user_data(id)'.$days_str.')');
			foreach($user as $row){
				$pdo->query('INSERT INTO '.$table_name.' VALUES('.$row["id"].$space_str.')');
				array_push($user_names,$row['name']);
			}

			$fw=fopen('../static/text/confirmed.txt','w');
			fwrite($fw,'not_confirmed');
			fclose($fw);
			$fw=fopen('../static/text/hide.txt','w');
			fwrite($fw,'not_hide');
			fclose($fw);

			$prev_3_month;
			if($month<=3){
				$prev_3_month=$month+12-3;
			}
			else{
				$prev_3_month=$month-3;
			}
			$prev_3_table_name='shift_month'.$prev_3_month;
			$pdo->query('DROP TABLE IF EXISTS '.$prev_3_table_name);
		}
	}
	catch(PDOException $e){
		echo '接続失敗:'.$e;
	}
	finally{
		$pdo=null;
	}


	try{
		$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
		$count=$pdo->query('SELECT * FROM '.$table_name);
		$employee_count=$count->rowCount();
	}
	catch(PDOException $e){
		echo '接続失敗:'.$e;
	}
	finally{
		$pdo=null;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<mata charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title>豊萬シフト共有・管理</title>
		<link rel="stylesheet" href="../static/shift.css">
		<script src="../static/jquery-3.4.1.min.js"></script>
		<script src="../static/sweetalert2.js"></script>
		<!--<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">-->

		<script>
			let username="<?php echo $_SESSION['name']; ?>";
			let id="<?php echo $_SESSION['id']; ?>";
			let days=<?php echo $days; ?>;
			let table_name="<?php echo $table_name; ?>";

			//誰もいない日を判別し、クラスにnobodyを追加
			function updateTableSelector(){
				for(let i=1;i<=days;i++){
					let str=$('td[id$="_'+i+'"]').text();
					if(str.indexOf('○')!==-1){continue};
					if(str.indexOf('16:30')!==-1){continue};
					if(str.indexOf('17:30')!==-1){continue};
					if(str.indexOf('18:00')!==-1){continue};
					if(str.indexOf('18:30')!==-1){continue};

					$('td[id$="_'+i+'"]').addClass("nobody");
				}
			}
		</script>

		<script>
			$(function(){
				updateTableSelector();

				//店長ログイン時
				if(<?php echo $_SESSION['id']; ?>==1){
					$('#all_ok').hide();
					for(let i=5;i<=<?php echo $employee_count; ?>;i++){
						let p_day=$('select[name="day"]').val();
						let str=$('#'+i+'_'+p_day).text();
						let part_name=$('#'+i).text();
						if(str==='○' || str==='16:30' || str==='17:30' || str==='18:00' || str==='18:30'){
							$('select[name="full_time_employee"]').append('<option value="'+i+'">'+part_name+'</option>');
						}
					}
				}

				//確定シフト、提出シフト表示判別変数
				<?php
				$fr=fopen('../static/text/confirmed.txt','r');
				$confirmed=fgets($fr);
				$confirmed=str_replace($line_array,'',$confirmed);
				fclose($fr);
				$fr=fopen('../static/text/hide.txt','r');
				$hide=fgets($fr);
				$hide=str_replace($line_array,'',$hide);
				fclose($fr);
				?>
				if('<?php echo $hide; ?>'==='hide' && <?php echo $_SESSION['id']; ?>!=1){
					$('#not_confirmed').hide();
					$('#countDown').hide();
					$('.hr').hide();
				}

				//編集機能非表示(店長のみ)
				if(<?php echo $day; ?><20 && <?php echo $_SESSION['id']; ?>==1){
					$('.select_day_time').hide();
					$('.ok_div').hide();
					$('#all_div').hide();
					$('#confirm').hide();
					$('#not_edit h2').text('20日以降に編集可能です');
				}

				//正社員(店長以外)
				if(<?php echo $_SESSION['id']; ?>>=2 && <?php echo $_SESSION['id']; ?><=4){
					$('#not_confirmed').hide();
					$('.select_day_time').hide();
					$('#countDown').hide()
					$('#submit').hide();
					$('.hr').hide();
				}

				//提出期限カウントダウン
				function limitCounter() {
					let timer = setInterval(function() {
						let now = new Date();
						let deadline = new Date(<?php echo $year ?>+"/"+<?php echo $prev_month ?>+"/19 23:59:59");
						let days_between = Math.ceil((deadline - now)/(1000*60*60*24));
						let ms = (deadline - now);
						if (ms >= 0) {
							let h = Math.floor(ms / 3600000);
							let h_ = h % 24;
							let m = Math.floor((ms - h * 3600000) / 60000);
							let s = Math.round((ms - h * 3600000 - m * 60000) / 1000);

							$('#countOutput').text(days_between+"日"+h_+"時間"+m+"分"+s+"秒");

							if ((h==0) && (m==0) && (s==0)) {
								clearInterval(timer);
								$('#countOutput').text("提出期限が過ぎました");
								if(<?php echo $_SESSION['id']; ?>!=1){
									$('#submit,.select_day_time,.ok_div,#all_div,#limit_text').hide();
								}
							}
						}else{
							if(<?php echo $_SESSION['id']; ?>!=1){
								$('#countOutput').text("提出期限が過ぎました");
								$('#submit,.select_day_time,.ok_div,#all_div,#limit_text').hide();
							}
						}
					}, 1000);
				}
				limitCounter();

				//シフト書き込み
				$('#ok').on('click',function(){
					let day=$('select[name="day"]').val();
					let time=$('select[name="time"]').val();
					if(day!=='-' && time!=='-'){
						$('.login'+day).html(time);
						$('td[id$="_'+day).removeClass('nobody');
						updateTableSelector();
					}
				});

				//全日程同時書き込み
				$('#all_ok').on('click',function(){
					let all=$('select[name="time"]').val();
					$('td[id^="'+$('select[name="full_time_employee"]').val()+'_"]').html(all);
					$('td[id^="'+id+'_"]').html(all);
					if(all==='×' || all===' '){
						updateTableSelector();
					}
					else{
						$('td').removeClass('nobody');
					}
				});

				//シフト送信,確定
				$('#submit').on('click',function(){
					Swal.fire({
						title: 'シフトを送信しますか？',
						text: "提出期限内であれば修正可能です",
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: '送信する',
						cancelButtonText:'キャンセル',
						reverseButtons:true
					}).then((result) => {
						if (result.value) {
							Swal.fire(
							'シフトを送信しました！',
							'',
							'success'
							)

							let shift_days=[];
							for(let i=1;i<=days;i++){
								shift_days.push($('#'+id+'_'+i).text());
							}
							$.ajax({
								dataType: 'text',
								type: 'POST',
								url: 'update_table.php',
								data: {
									id:id,
									days:days,
									table_name:table_name,
									shift : shift_days
								},
							}).done(function(data){
								console.log('通信成功！');	
							}).fail(function(data){
								alert('通信失敗');
							});
							//}
						}
					});
				});

				//シフト確定(店長専用)
				$('#confirm').on('click',function(){
					Swal.fire({
						title: 'シフトを確定して公開しますか？',
						text: "<?php echo $prev_month; ?>月のシフト表は表示されなくなります",
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: '公開する',
						cancelButtonText:'キャンセル',
						reverseButtons:true
					}).then((result) => {
						if (result.value) {
							Swal.fire(
							'シフトを公開しました！',
							'',
							'success'
							)

							let shift_days=[];
							for(let i=1;i<=<?php echo $employee_count; ?>;i++){
								for(let j=1;j<=days;j++){
									text=$('#'+i+'_'+j).text();
									shift_days.push(text);
								}

								$.ajax({
									dataType: 'text',
									type: 'POST',
									url: 'update_table.php',
									data: {
										id:i,
										days:days,
										table_name:table_name,
										shift : shift_days,
									},
								}).done(function(data){
									console.log('通信成功！');	
								}).fail(function(data){
									alert('通信失敗');
								});
								shift_days.splice(0);
							}
						}
					});
				});


				//[名前変更時]日付,時間セレクト動的変更(店長専用)
				$('select[name="full_time_employee"]').on('change',function(){
					id=$(this).val();
					$('.login_user').addClass("employee");
					$('.login_user').removeClass("login_user");
					for(let i=1;i<=days;i++){
						$('.login'+i).removeClass("login"+i);
					}
					$('#'+id).removeClass("employee")
					$('#'+id).addClass("login_user");
					for(let i=1;i<=days;i++){
						$('#'+id+'_'+i).addClass("login"+i);
					}
					
					let selected_day=$('select[name="day"]').val();
					let p_id=$('select[name="full_time_employee"]').val()
					$('select[name="day"] > option').remove();
					$('select[name="time"] > option').remove();

					//正社員
					if(p_id<=4){
						for(let i=1;i<=days;i++){
							if(selected_day==i){
								$('select[name="day"]').append('<option value="'+i+'" selected>'+i+'</option>');
							}
							else{
								$('select[name="day"]').append('<option value="'+i+'">'+i+'</option>');
							}
						}
					}
					//アルバイト
					else{
						for(let i=1;i<=days;i++){
							let str=$('#'+p_id+'_'+i).text();
							if(str==='○' || str==='16:30' || str==='17:30' || str==='18:00' || str==='18:30'){
								if(i==selected_day){
									$('select[name="day"]').append('<option value="'+i+'" selected>'+i+'</option>');
									$('select[name="time"]').append('<option selected>'+str+'</option>');
									$('select[name="time"]').append('<option>×</option>');
								}
								else{
									$('select[name="day"]').append('<option value="'+i+'">'+i+'</option>');
								}
							}
						}
					}

					if(p_id<=4){
						$('select[name="time"] > option').remove();
						$('select[name="time"]').append('<option>○</option>');
						$('select[name="time"]').append('<option>×</option>');
						$('select[name="time"]').append('<option>△</option>');
					}
				});

				//[日付変更時]名前,時間セレクト動的変更(店長専用)
				$('select[name="day"]').on('change',function(){
					if(<?php echo $_SESSION['id'] ?>!=1){
						return;
					}
					let selected_id=$('select[name="full_time_employee"]').val();
					let p_id=$('select[name="full_time_employee"]').val()
					for(let i=5;i<=<?php echo $employee_count; ?>;i++){
						let p_day=$('select[name="day"]').val();
						let str=$('#'+i+'_'+p_day).text();
						let part_name=$('#'+i).text();
						$('select[name="full_time_employee"] > option[value="'+i+'"]').remove();
						if(p_day==='0'){
							if(i==selected_id){
								$('select[name="full_time_employee"]').append('<option value="'+i+'" selected>'+part_name+'</option>');
							}
							else{
								$('select[name="full_time_employee"]').append('<option value="'+i+'">'+part_name+'</option>');
							}
							continue;
						}
						if(str==='○' || str==='16:30' || str==='17:30' || str==='18:00' || str==='18:30'){
							if(i==selected_id){
								$('select[name="full_time_employee"]').append('<option value="'+i+'" selected>'+part_name+'</option>');
								$('select[name="time"] > option').remove();
								$('select[name="time"]').append('<option selected>'+str+'</option>');
								$('select[name="time"]').append('<option>×</option>');
							}
							else{
								$('select[name="full_time_employee"]').append('<option value="'+i+'">'+part_name+'</option>');
							}
						}
					}
					if(p_id<=4){
						$('select[name="time"] > option').remove();
						$('select[name="time"]').append('<option>○</option>');
						$('select[name="time"]').append('<option>×</option>');
						$('select[name="time"]').append('<option>△</option>');
					}
				})
			});
		</script>
	</head>
	<body>
		<div class="wrap">
			<header>
				<h1 id="title">豊萬シフト共有・管理</h1>
				<h5 class="login_now">ログイン中：<?php echo $_SESSION['name'] ?>さん</h5>
			</header>
			<?php 
			$print_headline;
			if($_SESSION['id']!=1){
				$print_headline='提出';
			}
			else{
				if($day>=20){
					$print_headline='編集';
				}
				else{
					$print_headline='提出状況';
				}
			} ?>
			<div id="not_confirmed">
				<div class="headline_div">
					<h2 class="headline">　<?php echo $month; ?>月シフト<?php echo $print_headline; ?>　</h2>
				</div>
				<div class="scroll">
					<table>
						<tr>
							<th rowspan="2" id="month"><?php echo $month; ?>月</th>
							<?php for($i=1;$i<=$days;$i++){ ?>
							<td class="day"><?php echo $i; ?></td>
							<?php } ?>
						</tr>
						<tr>
								<?php for($i=1;$i<=$days;$i++){ ?>
									<?php $timestanp=mktime(0,0,0,$month,$i,$year); ?>
									<?php if(date('w',$timestanp)==0){ ?>
										<td class="week sunday"><?php echo $week[date('w',$timestanp)]; ?></td>
									<?php } else if(date('w',$timestanp)==6) { ?>
										<td class="week saturday"><?php echo $week[date('w',$timestanp)]; ?></td>
									<?php } else { ?>
									<td class="week"><?php echo $week[date('w',$timestanp)]; ?></td>
									<?php } ?>
								<?php } ?>
						</tr>
						<?php
						$employee_names=[];
						$employee_ids=[];
						try{
							$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
							$sql;
							#店長
							if($_SESSION['id']==1){
								$sql=$pdo->query('SELECT * FROM user_data INNER JOIN '.$table_name.' ON user_data.id='.$table_name.'.id'); ?>
							<?php }
							#その他
							else{
								$sql=$pdo->query('SELECT * FROM user_data INNER JOIN '.$table_name.' ON user_data.id='.$table_name.'.id WHERE user_data.id>=5'); ?>
							<?php } ?>
							<?php foreach($sql as $row){ ?>
								<?php array_push($employee_names,$row['name']); ?>
								<?php array_push($employee_ids,$row['id']); ?>
								<?php if($row['id']==$_SESSION['id']) { ?>
									<tr>
										<th class="login_user" id=<?php echo $row['id']; ?>><?php echo $row['name']; ?></th>
										<?php for($i=1;$i<=$days;$i++){ ?>
											<td class="login<?php echo $i; ?>" id="<?php echo $row['id'].'_'.$i; ?>"><?php echo $row['day'.$i]; ?></td>
										<?php } ?>
									</tr>
								<?php } else{ ?>
									<tr>
										<th class="employee" id=<?php echo $row['id']; ?>><?php echo $row['name']; ?></th>
										<?php for($i=1;$i<=$days;$i++){ ?>
											<td id="<?php echo $row['id'].'_'.$i?>"><?php echo $row['day'.$i]; ?></td>
										<?php } ?>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php }
						catch(PDOException $e){
							echo '接続失敗:'.$e;
						}
						finally{
							$pdo=null;
						}
						?>
					</table>
				</div>
			</div>
			<br>
			<div class="select_day_time">
				<?php if($_SESSION['id']==1){ ?>
				<h5 class="inline">従業員を選択</h5>
				<select name="full_time_employee">
					<?php for($i=0;$i<4;$i++){ ?>
					<option value="<?php echo $employee_ids[$i]; ?>"><?php echo $employee_names[$i]; ?></option>
					<?php } ?>
				</select>
				<br><br>
				<?php } ?>
				<h5 class="inline">日付を選択</h5>
				<select name="day">
					<?php for($i=1;$i<=$days;$i++){?>
					<option value=<?php echo $i ?>><?php echo $i; ?></option>
					<?php } ?>
				</select>
				<h5 class="inline">時間を選択</h5>
				<select name="time">
					<option>○</option>
					<option>×</option>
					<?php
					if($_SESSION['id']==1){echo '<option>△</option>';}
					else{
						echo '<option>16:30</option>';
						echo '<option>17:30</option>';
						echo '<option>18:00</option>';
						echo '<option>18:30</option>';
					}
					?>
				</select>
				<div class="write">
					<div class="ok_div">
						<button type="button" id="ok">書き込む</button>
					</div>
					<button type="button" id="all_ok">全て書き込む</button>
				</div>
			</div>
			<br><br>

			<?php if($_SESSION['id']==1){ ?>
				<div style="text-align: center;">
					<button type="button" id="confirm">確定</button>
				</div>
			<?php } else{?>
				<div style="text-align: center;">
					<button type="button" id="submit">送信</button>
				</div>
				<br>
				<div id="countDown">
					<h4 id="limit"><span id="limit_text">提出期限まで残り </span></h4><h2 id="countOutput"></h2>
				</div>
			<?php } ?>
			<div id="not_edit" style="text-align: center;"><h2></h2></div>

			<div class="hr"><hr></div>
			<div id="confirmed">
				<div class="headline_div">
					<h2 class="headline">　<?php echo $prev_month; ?>月シフト表　</h2>
				</div>
				<div class="scroll">
					<table>
						<tr>
							<th rowspan="2" id="month"><?php echo $prev_month; ?>月</th>
							<?php 
							$prev_days;
							$prev_table_name='shift_month'.$prev_month;
							try{
								$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']); 
								$sql=$pdo->query('SELECT * FROM user_data INNER JOIN '.$prev_table_name.' ON user_data.id='.$prev_table_name.'.id');
								$prev_days=$sql->columnCount();
								$prev_days-=4;
							}
							catch(PDOException $e){
								echo '接続失敗:'.$e;
							}
							finally{
								$pdo=null;
							} ?>
							<?php for($i=1;$i<=$prev_days;$i++){ ?>
							<td class="day"><?php echo $i; ?></td>
							<?php } ?>
						</tr>
						<tr>
								<?php for($i=1;$i<=$prev_days;$i++){ ?>
									<?php $timestanp=mktime(0,0,0,$prev_month,$i,$year); ?>
									<?php if(date('w',$timestanp)==0){ ?>
										<td class="week sunday"><?php echo $week[date('w',$timestanp)]; ?></td>
									<?php } else if(date('w',$timestanp)==6) { ?>
										<td class="week saturday"><?php echo $week[date('w',$timestanp)]; ?></td>
									<?php } else { ?>
									<td class="week"><?php echo $week[date('w',$timestanp)]; ?></td>
									<?php } ?>
								<?php } ?>
						</tr>
						<?php
						try{
							$pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
							$sql=$pdo->query('SELECT * FROM user_data INNER JOIN '.$prev_table_name.' ON user_data.id='.$prev_table_name.'.id ORDER BY user_data.id'); ?>
						<?php foreach($sql as $row){ ?>
							<?php array_push($employee_names,$row['name']); ?>
							<?php array_push($employee_ids,$row['id']); ?>
							<?php if($row['id']==$_SESSION['id']) { ?>
								<tr>
									<th class="login_user_prev" id="<?php echo $row['id']; ?>_prev"><?php echo $row['name']; ?></th>
									<?php for($i=1;$i<=$prev_days;$i++){ ?>
										<?php 
										if($row['id']>=4){
											if($row['day'.$i]=='○'){
												$row['day'.$i]='17:00';
											}
											else if($row['day'.$i]=='×' || $row['day'.$i]==' '){
												$row['day'.$i]='休';
											}
										}
										?>
										<?php 
											#当日
											if($i==date('d')){ ?>
											<td class="login<?php echo $i; ?> today_login" id="prev_<?php echo $row['id'].'_'.$i; ?>_"><?php echo $row['day'.$i]; ?></td>
										<?php } else{ ?>
											<td class="prev_login<?php echo $i; ?>" id="prev_<?php echo $row['id'].'_'.$i; ?>_"><?php echo $row['day'.$i]; ?></td>
										<?php } ?>
									<?php } ?>
								</tr>
							<?php } else{ ?>
								<tr>
									<th class="employee" id="prev_<?php echo $row['id']; ?>_"><?php echo $row['name']; ?></th>
									<?php for($i=1;$i<=$prev_days;$i++){ ?>
										<?php 
										if($row['id']>=4){
											if($row['day'.$i]=='○'){
												$row['day'.$i]='17:00';
											}
											else if($row['day'.$i]=='×' || $row['day'.$i]==' '){
												$row['day'.$i]='休';
											}
										}
										?>
										<?php if($i==date('d')){ ?>
											<td id="prev_<?php echo $row['id'].'_'.$i?>_" class="today"><?php echo $row['day'.$i]; ?></td>
										<?php } else{ ?>
											<td id="prev_<?php echo $row['id'].'_'.$i?>_"><?php echo $row['day'.$i]; ?></td>
										<?php } ?>
									<?php } ?>
								</tr>
							<?php }
							}
						}
						catch(PDOException $e){
							echo '接続失敗:'.$e;
						}
						finally{
							$pdo=null;
						}
						?>
					</table>
				</div>
			</div>

			<?php require '../template/shift_footer.php'; ?>
		</div>
	</body>
</html>