<?php
namespace phpBlog;
/**
 * Description of dbconnect
 *
 * @author dvkrasavin
 */
trait dbconnect {
    private $config;
    private $connectionParams;
    private $conn;

    public function dbconnect(){
        if (isset($GLOBALS['SysValue']) && 
            isset($GLOBALS['SysValue']['connect']['dbase']) &&
            isset($GLOBALS['SysValue']['connect']['user_db']) &&
            isset($GLOBALS['SysValue']['connect']['pass_db']) &&
            isset($GLOBALS['SysValue']['connect']['host']) &&
            isset($GLOBALS['SysValue']['connect']['driver'])         
            ){
            $this->config = new \Doctrine\DBAL\Configuration();
            $this->connectionParams = array(
                'dbname' => $GLOBALS['SysValue']['connect']['dbase'],
                'user' => $GLOBALS['SysValue']['connect']['user_db'],
                'password' => $GLOBALS['SysValue']['connect']['pass_db'],
                'host' => $GLOBALS['SysValue']['connect']['host'],
                'driver' => $GLOBALS['SysValue']['connect']['driver'],
            );
            //соединяемся с базой данных
            $this->conn= mysqli_connect($this->connectionParams['host'], $this->connectionParams['user'], $this->connectionParams['password']) or die ($GLOBALS['mysqlConnectionError']);
            mysqli_select_db($this->conn,$this->connectionParams['dbname']) or @die ($GLOBALS['mysqlConnectionError']);
            mysqli_query($this->conn,"set names utf8mb4");

        } else
            echo $GLOBALS['iniFileError'];
    }
    public function getConnectionParams(){
        return $this->connectionParams;
    }
}
