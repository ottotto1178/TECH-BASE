<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>この掲示板のテーマ</h1>
  <p>好きな食べ物を教えてください</p>
  <?php
    // データベース接続～テーブル作成
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS forum_table"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY," //AUTO_INCREMENT->自動採番(登録されたデータに自動的に連番を格納する)
    . "name char(10),"
    . "comment TEXT,"
    . "date TIMESTAMP,"
    . "password text"
    .");";
    $stmt = $pdo->query($sql);
    //新規追加=>完成
    $upload_time = date('Y/m/d H:i:s');
    if(!empty($_POST['name']) && empty($_POST['edit_num'])){
      $sql = $pdo -> prepare("INSERT INTO forum_table (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
      $sql -> bindParam(':name', $name, PDO::PARAM_STR);
      $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
      $sql -> bindParam(':date', $date, PDO::PARAM_STR);
      $sql -> bindParam(':password', $password, PDO::PARAM_STR);
      $name = $_POST['name'];
      $comment = $_POST['comment'];
      $date = $upload_time;
      $password = $_POST['password'];
      $sql -> execute();
    }
    // 編集=>
    elseif(!empty($_POST['name']) && !empty($_POST['edit_num'])){
      $edit_num = $_POST['edit_num'];
      $id = $edit_num;
      $name = $_POST['name'];
      $comment = $_POST['comment'];
      $date = date('Y/m/d H:i:s');
      $password = $_POST['password'];
      $sql = 'UPDATE forum_table SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id AND password=:password';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':name', $name, PDO::PARAM_STR);
      $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
      $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
      $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->bindParam(':password', $password, PDO::PARAM_STR);
      $stmt -> execute();
    }
    // 削除=>完成
    elseif(!empty($_POST['del'])){
      $del_num = $_POST['del'];
      $id = $del_num;
      $password = $_POST['del_pass'];
      $sql = 'DELETE FROM forum_table WHERE id=:id AND password=:password';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->bindParam(':password', $password, PDO::PARAM_STR);
      $stmt->execute();
    }

    // 編集フォーム=>完成
    elseif(!empty($_POST['edit'])){
      $edit_num = intval($_POST['edit']);
      $id = $edit_num;
      $password = $_POST['edit_pass'];
      $sql = 'SELECT * FROM forum_table WHERE id=:id ';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      $results = $stmt->fetchAll();
      foreach ($results as $row){
        $edit_pass = $row['password'];
        if($password == $edit_pass){
          $edit_num = $row['id'];
          $edit_name =  $row['name'];
          $edit_comment = $row['comment'];
        }
      }
    }
  ?>
  <form method="post">
  <input type="text" name='name' placeholder='名前' value="<?php if(isset($edit_name)) {echo $edit_name;} ?>">
    <input type="text" name='comment' placeholder='コメント' value="<?php if(isset($edit_comment)) {echo $edit_comment;} ?>">
    <input type="text" name='password' placeholder='パスワード' value="<?php if(isset($edit_pass)) {echo $edit_pass;} ?>">
    <input type="submit" name='submit'>
    <input type="text" name='del' placeholder='削除対象番号'>
    <input type="text" name='del_pass' placeholder='パスワード'>
    <input type="submit" name='submit' value='削除'>
    <input type="text" name='edit' placeholder='編集対象番号'>
    <input type="text" name='edit_pass' placeholder='パスワード'>
    <input type="hidden" name='edit_num' placeholder='edit_num' value="<?php if(isset($edit_num)) {echo $edit_num;} ?>">
    <input type="submit" name='submit' value='編集'>
  </form>
  <?php
    // 出力
    $sql = 'SELECT * FROM forum_table';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
      echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].',';
      echo $row['date'].'<br>';
      echo "<hr>";
    }
  ?>
</body>
</html>