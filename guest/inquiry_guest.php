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
            <div id="inquiry">
                <div class="headline_div">
                    <h2 class="headline">　ご意見・お問い合わせ　</h2>
                    <p>
                        ご要望や質問、感想などをどんどん送ってください！<br>
                    </p>
                    <p>
                        ※内容は匿名で送信されます。
                    </p>
                </div>
                <div class="text_form">
                    <form action="send.php" method="post">
                        <textarea name="content" cols="100" rows="10"></textarea>
                        <div id="send_button">
                            <input type="submit" value="送信">
                        </div>
                    </form>
                </div>
            </div>

            <?php require '../template/shift_footer_guest.php'; ?>
        </div>
    </body>
</html>