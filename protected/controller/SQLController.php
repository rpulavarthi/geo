<?php
 
class SQLController extends DooController {
 
        function query(){
 
                //echo $_POST['ip']; exit;
                $ip = $_POST['ip'];
                $db = $_POST['db'];
                $sql = $_POST['sql'];
                $username = 'developer';
                $password ='Code@3730';
                //$username = 'root';
                //$password = '';
 
                //echo $sql; exit;
 
                $dbh = new PDO("mysql:host=$ip;dbname=$db", $username, $password, array(PDO::ATTR_PERSISTENT => false, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE));
 
                $sth = $dbh->prepare($sql);
                $sth->execute();
 
                echo json_encode($sth->fetchAll(PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK);
        }
 
}
 
?>
