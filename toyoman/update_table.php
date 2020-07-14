<?php
    session_start();
    if(!isset($_SESSION['id'])){
        header('Location: login.php');
        exit;
    }
    require_once('../static/sec/mysql_info.php');
?>

<?php
    try{
        $pdo=new PDO('mysql:host=localhost;dbname='.$mysql_info['dbname'].';charset=utf8',$mysql_info['user'],$mysql_info['pass']);
        for($i=1;$i<=(int)$_POST['days'];$i++){ 
            $pdo->query('UPDATE '.$_POST['table_name'].' SET day'.$i.'="'.$_POST['shift'][$i-1].'" WHERE id='.$_POST['id']);
        }
    }
    catch(PDOException $e){
        echo '接続失敗:'.$e;
    }
    finally{
        $pdo=null;
    }

    if($_POST['id']==1){
        $fw=fopen('confirmed.txt','w');
        fwrite($fw,'confirmed');
        fclose($fw);
        $fw=fopen('static/text/hide.txt','w');
        fwrite($fw,'hide');
        fclose($fw);
    }
?>

