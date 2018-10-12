<?php
//データベース接続
try {
$pdo = new PDO('mysql:host=localhost;dbname=データベース名;charset=utf8','ユーザー名','パスワード',
array(PDO::ATTR_EMULATE_PREPARES => false));
} catch (PDOException $e) {
 exit('データベース接続失敗。'.$e->getMessage());
}
//テーブル作成
$sql="CREATE TABLE btable"
."("."num int(5),"
."name char(32),"
."comment TEXT,"
."time char(50),"
."pass char(32)"
.");";
$stmt = $pdo->query($sql);
//送信されたもの
$name = $_POST['name'];
$comment = $_POST['comment'];
$delnum = $_POST['delnum'];
$editnumber = $_POST['editnumber'];
$wanteditnum = $_POST['wanteditnum'];
$pass = $_POST['pass'];
$delpass = $_POST['delpass'];
$editpass = $_POST['editpass'];
	//新規投稿
	if(empty($wanteditnum) and !empty($comment) and  !empty($pass) and !empty($name)){
		//投稿番号の取得
		$sql = 'SELECT MAX(num) AS num_max FROM btable';
                $result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
                $num = $result['num_max']+1;
		$time=date("Y/m/d H:i:s");
		$sql = $pdo-> prepare("INSERT INTO btable(num,name,comment,time,pass)VALUES(:num,:name,:comment,:time,:pass)");
		$sql->bindParam(':num',$num,PDO::PARAM_INT);
     		$sql->bindParam(':name',$name,PDO::PARAM_STR);
		$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql->bindParam(':time',$time,PDO::PARAM_STR);
		$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
		$sql->execute();
		$sql="SELECT * FROM btable ORDER BY num";
     		$result=$pdo->query($sql);
	}
	//削除機能
	if(!empty($delnum) and  !empty($delpass)){
		//パスワードが正しいかどうか
		$value = $delnum;
		$sql='SELECT * FROM btable where num = :delnum';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':delnum', $value, PDO::PARAM_INT);
		$stmt -> execute();
		$row = $stmt->fetch();
		$pass = $row['pass'];
			if($pass == $delpass){;
				$value = $delnum;
				$sql = 'DELETE FROM btable where num = :delnum';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindParam(':delnum', $value, PDO::PARAM_INT);
				$stmt -> execute();
			}
	}
	//編集機能モードへ
	if(!empty($editnumber) and  !empty($editpass)){
		//パスワードが正しいかどうか
		$value = $editnumber;
		$sql='SELECT * FROM btable where num = :editnumber';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':editnumber', $value, PDO::PARAM_INT);
		$stmt -> execute();
		$row = $stmt->fetch();
		$pass = $row['pass'];
			if($pass == $editpass){;
				$value = $editnumber;
				$sql='SELECT * FROM btable where num = :editnumber';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindParam(':editnumber', $value, PDO::PARAM_INT);
				$stmt -> execute();
				$row = $stmt->fetch();
				$editnum = $row['num'];
				$namae = $row['name'];
				$comento = $row['comment'];
			}
	}
	//編集機能編集モードで投稿されたとき
	if(!empty($wanteditnum) and  !empty($pass) and !empty($name) and !empty($comment) ){
		$value = $wanteditnum;
		$namew = $name;
		$commentw = $comment;
		$passw = $pass;
		$sql="UPDATE  btable set name='$namew',comment='$commentw',pass='$passw'where num = :wanteditnum";
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':wanteditnum', $value, PDO::PARAM_INT);
		$stmt -> execute();
		$row = $stmt->fetch();
	}
?>

<html>
<head>
<meta charset="utf-8">
</head>
<body>
<form method="POST" action="bulletinboard.php">
	<input type="hidden" value = "<?php echo $editnum; ?>" name="wanteditnum" /><br />
	名前：
	<input type="text" value = "<?php echo $namae; ?>" name="name" /><br />
	コメント：
	<input type="text" value = "<?php echo $comento; ?>" name="comment" /><br />
	パスワード:
	<input type="text"  name="pass" />
	<input type="submit" value="送信" /><br />
</form>
</body>
<body>
<form method="POST" action="bulletinboard.php">
	<input type="text" placeholder = "削除対象番号" name="delnum" />
	<input type="text"  placeholder = "パスワード "name="delpass"/>
	<input type="submit" value="削除" /><br />
</form>
</body>
<body>
<form method="POST" action="bulletinboard.php">
	<input type="text" placeholder = "編集対象番号" name="editnumber" />
	<input type="text" placeholder = "パスワード"  name="editpass" />
	<input type="submit" value="編集" /><br />
</form>
</body>					

<?php
$sql='SELECT * FROM btable ORDER BY num';
$results=$pdo->query($sql);
     foreach($results as $row){
        echo "投稿番号：{$row['num']} 名前：{$row['name']} コメント：{$row['comment']} 投稿日：{$row['time']}"."<br>";
     }
?>

