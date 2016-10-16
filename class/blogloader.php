<?php
namespace phpBlog;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Symfony\Component\VarDumper\VarDumper::dump(array(filter_input(INPUT_SERVER,'DOCUMENT_ROOT')));
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/parseini.php';
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/dbroutine.php';

use phpBlog\parseini as parseini;
use phpBlog\dbroutine as dbroutine;
use Doctrine\DBAL\DriverManager;
use \Doctrine\DBAL\Query\QueryBuilder;
use \Symfony\Component\VarDumper\VarDumper;

/**
 * Description of blogloader
 *
 * @author dkrasavin
 */
class blogloader {
    //класс чтения конфиг файла
    use parseini;
    //класс работы с базой данных
    private $dbroutine;
    
    public function __construct(){
        //глобально открываем содержимое конфиг файла
        self::parseini();
        //инициализируем файл работы с БД
        $this->dbroutine=new dbroutine();
        //активируем соединение с БД
        $this->dbroutine->dbconnect();
    }
    
    public function getSessionInfo(){
        //для открытой сессии сохраняем id
        $_SESSION['sessionId']= session_id();

        //если такая сессия уже есть 
        if ($this->dbroutine->get_row_logged_user($_SESSION['sessionId'])) {
            //получаем количество попыток логина пользователя
            $attempts=$this->dbroutine->get_user_login_attempts($_SESSION['sessionId'],$_SESSION['sessionId']);
            $this->dbroutine->update_row_logged_user($_SESSION['sessionId'],$_SESSION['sessionId'],$attempts+1);
        } else
            $this->dbroutine->add_row_logged_user($_SESSION['sessionId'],$_SESSION['sessionId'],1);
        if (isset($attempts))
            return $attempts;
        else
            return 0;
    }
    
   public function get_total_sessions_amount(){
       return $this->dbroutine->get_total_sessions_amount();
   }
   public function doctrine_get_user($user=null,$pwd=null) {
       if (!is_null($user) && !is_null($pwd)){
            $config = new \Doctrine\DBAL\Configuration();
            $config->setAutoCommit(true);
            $connectionParams=$this->dbroutine->getConnectionParams();
            $conn = DriverManager::getConnection($connectionParams, $config);
             $qb = $conn->createQueryBuilder()
             ->select('count(u.id) hasuser,u.id id')
             ->from('fos_user', 'u')
                     ->where('u.u=:usr AND u.p=:pwd');
             $qb->setParameter('usr', $user);
             $qb->setParameter('pwd', $pwd);
             $results = $qb->execute();
             if ($GLOBALS['SysValue']['debug']['debug'])
                 VarDumper::dump(array('conn'=>$conn,'qb'=>$qb,'results'=>$results));
             return $results->fetchAll();
       } else {
           return 0;           
       }
   }
}
