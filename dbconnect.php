<?php
function open_db()
{

  if(!$_SERVER['RDS_MY_DB_NAME']) {
    $dbhost = 'aa247qpcanfzbf.cjbydkyc8ulh.ap-northeast-1.rds.amazonaws.com';
    $dbport = 3306;
    $dbname = 'water';

    $dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname}";
    $username = 'izumiken';
    $password = '1zum1ken';

  } else {
    $dbhost = $_SERVER['RDS_HOSTNAME'];
    $dbport = $_SERVER['RDS_PORT'];
    $dbname = $_SERVER['RDS_MY_DB_NAME'];

    $dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname}";
    $username = $_SERVER['RDS_USERNAME'];
    $password = $_SERVER['RDS_PASSWORD'];

  }
  error_log($dbhost);
  error_log($dbport);
  error_log($dbname);
  error_log($username);
  error_log($password);

  return mysqli_connect($dbhost, $username, $password, $dbname, $dbport);
}
?>
