 <!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>掲示板</title>
        <style>

        </style>
    </head>
    <body>

        <form method="post" action="">
            [コメントの入力]<br>
            <input type="text" name="comment1" value="" placeholder="名前を入力">
            <input type="text" name="comment2" value="" placeholder="コメントを入力">
            <input type="password" name="password" value="" placeholder="パスワードを入力">
            <input type="submit" name="submit_post" value="送信">
            <br><br>
            [コメントの削除]<br>
            <input type="text" name="comment3" value="" placeholder="削除したい番号を入力">
            <input type="password" name="delPassword" value="" placeholder="パスワードを入力">
            <input type="submit" name="submit_delete" value="削除">
            <br><br>
            [コメントの編集]<br>
            <input type="text" name="editNo" placeholder="編集したい番号">
            <input type="password" name="editPassword" value="" placeholder="パスワードを入力">
            <input type="text" name="editName" placeholder="新しい名前">
            <input type="text" name="editComment" placeholder="新しいコメント">
            <input type="submit" name="edit" value="編集">
            <br><br><br><br>
        </form>

        <?php
        // DB接続設定
        $dsn = getenv('DB_DSN');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        $sql = "CREATE TABLE IF NOT EXISTS boad"
            ." ("
            . "number INT AUTO_INCREMENT PRIMARY KEY,"
            . "name CHAR(32),"
            . "comment TEXT,"
            . "time TEXT,"
            . "password TEXT"
            .");";
 
        $pdo->query($sql);
        
        
        //編集
        if (isset($_POST["edit"]) && !empty($_POST["editNo"]) && !empty($_POST["editName"]) && !empty($_POST["editComment"]) && !empty($_POST["editPassword"])) {
            $editNo = $_POST["editNo"];
            $editName = $_POST["editName"];
            $editComment = $_POST["editComment"];
            $time = date("Y-m-d H:i:s");
            $editPassword = $_POST["editPassword"];
            
            $sql = "SELECT password FROM boad WHERE number = :number";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':number', $editNo, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            
            if($row["password"] === $editPassword){
                $sql = "UPDATE boad SET name = :name, comment = :comment, time = :time WHERE number = :number";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":name", $editName, PDO::PARAM_STR);
                $stmt->bindParam(":comment", $editComment, PDO::PARAM_STR);
                $stmt->bindParam(":time", $time, PDO::PARAM_STR);
                $stmt->bindParam(":number", $editNo, PDO::PARAM_INT);
                $stmt->execute();
            }else{
                echo "パスワードが違います<br>";
            }
        }
        
        //削除
        if(isset($_POST["submit_delete"]) && !empty($_POST["comment3"]) && !empty($_POST["delPassword"])){
            $id = $_POST["comment3"];
            $delPassword = $_POST["delPassword"];
            
            $sql = "SELECT password FROM boad WHERE number = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            
            if($row["password"] === $delPassword){
                $sql = 'delete from boad where number=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
                $stmt->execute();
            
                $sql = 'SELECT * FROM boad';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
            }else{
                echo "パスワードが違います<br>";
            }
        }

        //投稿
        if(!empty($_POST["comment1"]) && !empty($_POST["comment2"]) && !empty($_POST["password"])) {
            $name = $_POST["comment1"];
            $comment = $_POST["comment2"];
            $time = date("Y-m-d H:i:s");
            $key = $_POST["password"];
        
        //$name = '（好きな名前）';
        //$comment = '（好きなコメント）'; //好きな名前、好きな言葉は自分で決めること
            
            $sql = "INSERT INTO boad (name, comment, time ,password) VALUES (:name, :comment, :time, :password)";
            $stmt = $pdo->prepare($sql);
            //プレースホルダに変数を宛がう
            //$stmt->bindParam(':number', $number, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':time', $time, PDO::PARAM_STR);
            $stmt->bindParam(':password', $key, PDO::PARAM_STR);
            $stmt->execute();
        }
        
        // データ表示
        $sql = "SELECT * FROM boad ORDER BY number ASC";
        $stmt = $pdo->query($sql);
        
        
        if ($stmt->rowCount() > 0){
            echo "<table border='2' style='width:80%; font-size:20px;'>";
            echo "<tr><th>投稿番号</th><th>名前</th><th>コメント</th><th>投稿日時</th></tr>";
        
            foreach($stmt as $row){
                echo "<tr>";
                echo "<td>" . $row['number'] . "</td>";
                echo "<td><span style='color:red;'>" . $row['name'] . "</span></td>";
                echo "<td><strong>" . $row['comment'] . "</strong></td>";
                echo "<td>" . $row['time'] . "</td>";
                echo "</tr>";
                //$linenumber++;
            }
        echo "</table>";
        }

        ?>

    </body>
</html>