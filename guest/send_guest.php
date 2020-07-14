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
        <div class="wrap">
            <header>
                <h1 id="title">豊萬シフト共有・管理</h1>
                <h5 class="login_now">ログイン中：<?php echo $_SESSION['name'] ?>さん</h5>
            </header>
            <?php
                //本当は意見はメールで受け取りたいが、設定が少し複雑であるため後回し

                /*mb_language("Japanese");
                mb_internal_encoding("UTF-8");
                $to="songju19990426@gmail.com";
                $title="シフト管理・共有アプリ";
                $content=$_POST['content'];
                $headers = "From: from@example.com";
                $pfrom="-f ".$headers;
                if(mb_send_mail($to,$title,$content,$headers,$pfrom)){
                    echo '<h2>意見を承りました</h2>';
                }
                else{
                    echo '<h2>送信に失敗しました</h2>';
                }*/

                date_default_timezone_set('Asia/Tokyo');
                $fa=fopen('text_guest/inquiry.txt','a');
			    fwrite($fa,"[".date("Y/m/d H:i:s")."](ゲスト)".$_POST['content']."\n");
                fclose($fa);
            ?>
            <div class="inquiry_massage"> 
                <h2>意見を承りました</h2>
                <h2>ありがとうございます！</h2>
            </div>

            <?php require '../template/shift_footer.php'; ?>
        </div>
    </body>
</html>