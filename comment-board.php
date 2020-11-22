<?php
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = "CREATE TABLE IF NOT EXISTS board"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "pass char(32),"
    . "time datetime"
	.");";
    $stmt = $pdo->query($sql);

    if(!empty($_POST["password"])){
        $password = $_POST["password"];
    }

    if(!empty($_POST["del"])){
        $id = $_POST["del"];
        $sql = 'SELECT * FROM board';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row['id'] === $id){
                $pass = $row['pass'];
            }
        }
    }elseif(!empty($_POST["edit"])){
        $id = $_POST["edit"];
        $sql = 'SELECT * FROM board';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row['id'] === $id){
                $pass = $row['pass'];
            }
        }
    }

    if(empty($_POST["number"])){
        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])){
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $password = $_POST["password"];
            $created = date('Y-m-d H:i:s');
            $sql = $pdo -> prepare("INSERT INTO board (name, comment, pass, time) VALUES (:name, :comment, :pass, :time)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $password, PDO::PARAM_STR);
            $sql -> bindParam(':time', $created, PDO::PARAM_STR);
            $sql -> execute();
        }elseif(!empty($_POST["del"]) && $pass == $password){
            //削除機能
            $id = $_POST["del"];
            $sql = 'delete from board where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }elseif(!empty($_POST["edit"]) && $pass == $password){
            //編集モード
            $id = $_POST["edit"];
            $sql = 'SELECT * FROM board';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if($row['id'] === $id){
                    $editNumber = $row['id'];
                    $editName = $row['name'];
                    $editComment = $row['comment'];
                }
            }
            
        }
    }else{
        $id = $_POST["number"];
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $sql = 'UPDATE board SET name=:name,comment=:comment WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5</title>
</head>
<body>
<form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php if(!empty($_POST["edit"]) && $pass == $password){echo $editName;} ?>">
        <input type="text" name="comment" placeholder="コメント" value ="<?php if(!empty($_POST["edit"]) && $pass == $password){echo $editComment;} ?>">
        <input type="text" name="password" placeholder="パスワードの設定">
        <input type="hidden" name="number" value="<?php if(!empty($_POST["edit"]) && $pass == $password){echo $editNumber;} ?>">
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <input type="number" name="del" placeholder="削除するコメントの番号">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="submit" value="削除">
    </form>
    <form  action="" method="post">
        <input type="number" name="edit" placeholder="編集するコメントの番号">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="submit" value="編集">
    </form>
    <hr>
    <h1>この掲示板のテーマ：「動作確認をお願いします」</h1>
    <hr>
    <?php
        //ブラウザ表示
        $sql = 'SELECT * FROM board';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' '.$row['name'].' '.$row['time'].'<br>';
            echo $row['comment'].'<br>';
        echo "<hr>";
        }
    ?>
</body>
</html>
