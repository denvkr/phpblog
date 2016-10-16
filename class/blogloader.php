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
    //работа с doctrine
    private $doctrnconfig;
    private $connectionParams;
    private $doctrnconn;
    public function __construct(){
        //глобально открываем содержимое конфиг файла
        self::parseini();
        //инициализируем файл работы с БД
        $this->dbroutine=new dbroutine();
        //активируем соединение с БД
        $this->dbroutine->dbconnect();
        $this->doctrnconfig = new \Doctrine\DBAL\Configuration();
        $this->doctrnconfig->setAutoCommit(true);
        $this->connectionParams=$this->dbroutine->getConnectionParams();
        $this->doctrnconn = DriverManager::getConnection($this->connectionParams, $this->doctrnconfig);
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
   //получаем пользователя из БД
   public function doctrine_get_user($user=null,$pwd=null) {
       if (!is_null($user) && !is_null($pwd)){
             $qb = $this->doctrnconn->createQueryBuilder()
             ->select('count(u.id) hasuser,u.id id')
             ->from('fos_user', 'u')
                     ->where('u.u=:usr AND u.p=:pwd');
             $qb->setParameter('usr', $user);
             $qb->setParameter('pwd', $pwd);
             $results = $qb->execute();
             if ($GLOBALS['SysValue']['debug']['debug'])
                 VarDumper::dump(array('conn'=>$this->doctrnconn,'qb'=>$qb,'results'=>$results));
             return $results->fetchAll();
       } else {
           return 0;           
       }
   }
   
   public function doctrine_get_AllBlogs(){
             $qb = $this->doctrnconn->createQueryBuilder()
             ->select('b.id blogid,b.parentid blogparentid,b.userid,u.u bloguser,b.create,b.text blogtext')
             ->from('blogs', 'b')
             ->leftJoin('b','fos_user', 'u','u.id=b.userid');
             $results = $qb->execute();
             if ($GLOBALS['SysValue']['debug']['debug'])
                 VarDumper::dump(array('conn'=>$this->doctrnconn,'qb'=>$qb,'results'=>$results));
             return $results->fetchAll();
   }
   
   public function doctrine_add_post($blogparentid,$login,$login_id,$blogtext) {
             $createAt=new \DateTime();
             $qb = $this->doctrnconn->createQueryBuilder()
                     ->insert('blogs')->values(array('`parentid`'=>$blogparentid,'`userid`'=>$login_id,'`text`'=>"'".$blogtext."'",'`create`'=>"'".$createAt->format('Y-m-d H:i:s')."'"));
             $results = $qb->execute();
             if ($GLOBALS['SysValue']['debug']['debug'])
                 VarDumper::dump(array('conn'=>$this->doctrnconn,'qb'=>$qb,'results'=>$results));
             return $results->errorCode();
       
   }
}
