<footer>
    <div class="form_block">
        <a href="home_guest.php" class="footer_menu">ホーム</a>
    </div>
    <div class="form_block">
        <a href="manual_guest.php" class="footer_menu">使い方</a>
    </div>
    <div class="form_block">
        <a href="inquiry_guest.php" class="footer_menu">ご意見</a>
    </div>
    <div class="form_block">
        <a href="change_password_guest.php" class="footer_menu">パスワード変更</a>
    </div>
    <?php 
    if($_SESSION['id']==1){
        echo '<div class="form_block">';
        echo '<a href="register_guest.php" class="footer_menu">従業員編集</a>';
        echo '</div>';
    } ?>
    <div class="form_block">
        <a href="logout_guest.php" class="footer_menu">ログアウト</a>
    </div>
</footer>