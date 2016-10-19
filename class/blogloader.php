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
    //trait чтения конфиг файла
    use parseini;
    //класс работы с базой данных
    private $dbroutine;
    //работа с doctrine
    private $doctrnconfig;
    private $connectionParams;
    private $doctrnconn;
    public function __construct(){
        //глобально открываем содержимое конфиг файла
        if (!isset($GLOBALS['SysValue']))
            self::parseini();
        //инициализируем файл работы с БД
        $this->dbroutine=new dbroutine();
        //активируем соединение с БД
        $this->dbroutine->dbconnect();
        $this->doctrnconfig = new \Doctrine\DBAL\Configuration();
        $this->doctrnconfig->setAutoCommit(true);
        $this->connectionParams=$this->dbroutine->getConnectionParams();
        $this->doctrnconn = DriverManager::getConnection($this->connectionParams, $this->doctrnconfig);
        $qb = $this->doctrnconn->executeQuery('SET NAMES utf8mb4');
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
             $result=$results->fetchAll();
             $hasuser=$result[0]['hasuser'];
             $id=$result[0]['id'];
             if ($GLOBALS['SysValue']['debug']['debug'])
                 VarDumper::dump(array('conn'=>$this->doctrnconn,'qb'=>$qb,'result'=>$result,'hasuser'=>$hasuser,'id'=>$id));

             $result=array('hasuser'=>(int)$hasuser,'id'=>(int)$id);
             return $result;
       } else {
           return 0;           
       }
   }
   //получение блогов 
   //вариант с простой сортировкой
   public function doctrine_get_AllBlogs(){
             $qb = $this->doctrnconn->createQueryBuilder()
             ->select('b.id,b.parentid,b.userid,u.u bloguser,b.create,b.text blogtext,b.`sort`,CASE WHEN b.parentid=0 THEN \'root\' ELSE \'child\' END level')
             ->from('blogs', 'b')
             ->leftJoin('b','fos_user', 'u','u.id=b.userid')
                     ->orderBy('`sort`', 'ASC');
             $results = $qb->execute();
             if ($GLOBALS['SysValue']['debug']['debug'])
                 VarDumper::dump(array('conn'=>$this->doctrnconn,'qb'=>$qb,'results'=>$results));
             return $results->fetchAll();
   }
    //получение блогов 
    //вариант с последующем обходом и сортировкой дерева
   public function doctrine_get_AllBlogsSort(){
             $qb = $this->doctrnconn->createQueryBuilder()
             ->select('b.`id`,'
                     . 'b.`parentid`,'
                     . 'b.`userid`,'
                     . 'b.`user` bloguser,'
                     . 'b.`create`,'
                     . 'b.`text` blogtext,'
                     . 'b.`sort` sort,'
                     . '\'root\' `level`,'                     
                     . 'b.`childid`,'
                     . 'b.`childparentid`,'
                     . 'b.`childuserid`,'
                     . 'b.`childuser` childbloguser,'
                     . 'b.`childcreate`,'
                     . 'b.`childtext` childblogtext,'
                     . 'b.`childsort` childsort,'
                     . '\'child\' `childlevel`')
             ->from('blogssort', 'b');
             $results = $qb->execute();
             $results=$results->fetchAll();
             if ($GLOBALS['SysValue']['debug']['debug'])
                    VarDumper::dump(array('conn'=>$this->doctrnconn,'qb'=>$qb,'results'=>$results));
             //подготавливаем массив блогов
             if (!empty($results)){
                 //берем корневые элементы
                $rootBlogsArr=$this->buildBlogArray($results,0);
                if ($GLOBALS['SysValue']['debug']['debug'])
                   VarDumper::dump(array('rootBlogsArr'=>$rootBlogsArr));
                return $rootBlogsArr;
             }
             
   }
   //добавляем сообщение
   public function doctrine_add_post($blogparentid,$login,$login_id,$blogtext,$mainroot) {
             $createAt=new \DateTime();
             $createAtdb=$createAt->format('Y-m-d H:i:s');
             $qb = $this->doctrnconn->createQueryBuilder()
                     ->insert('blogs')->values(array('`parentid`'=>$blogparentid,'`userid`'=>$login_id,'`text`'=>"'".$blogtext."'",'`create`'=>"'".$createAtdb."'",'`sort`'=>$mainroot));
             $results = $qb->execute();
             //если пост- корень
             if ((int)$mainroot==0){
                $qb = $this->doctrnconn->createQueryBuilder()
                        ->update('blogs')->set('`sort`','`id`')->where('`parentid`=?')->andWhere('`userid`=?')->andWhere('`create`=?')->andWhere('`text`=>?');
                $qb->setParameter(0, $blogparentid);
                $qb->setParameter(1, $login_id);
                $qb->setParameter(2, $createAtdb);
                $qb->setParameter(3, $blogtext);
                if ($GLOBALS['SysValue']['debug']['debug'])
                   VarDumper::dump(array('rootBlogsArr'=>$qb->getSQL())); 
                $qb->execute();
             }
             if ($GLOBALS['SysValue']['debug']['debug'])
                 VarDumper::dump(array('conn'=>$this->doctrnconn,'qb'=>$qb,'results'=>$results));
             return $results;  
   }
   private function buildBlogArray($results,$stage,$retval=array(),$rootkeysarr=array(),$childkeysarr=array(),$parentid=0){
       if (!empty($results[$stage])){
           //корень
           if ((int)$results[$stage]['sort']===(int)$results[$stage]['id']){
                //проверяем если уже есть такой элемент
                if (in_array((string)$results[$stage]['id'],$rootkeysarr)===false){
                   $rootkeysarr[]=$results[$stage]['id'];
                   $retval[]=array_slice($results[$stage] , 0,8);
                }
                //потомки от корня
                if ((int)$results[$stage]['childparentid']==(int)$results[$stage]['id']  && !empty($results[$stage]['childparentid'])){
                   $childkeysarr[]=$results[$stage]['childid'];
                   $parentid=(int)$results[$stage]['childid'];
                   $retval[]=array('id'=>$results[$stage]['childid'],
                       'parentid'=>$results[$stage]['childparentid'],
                       'userid'=>$results[$stage]['childuserid'],
                       'bloguser'=>$results[$stage]['childbloguser'],
                       'create'=>$results[$stage]['childcreate'],
                       'blogtext'=>$results[$stage]['childblogtext'],
                       'sort'=>$results[$stage]['childsort'],
                       'level'=>$results[$stage]['childlevel']);
                }
                if ($GLOBALS['SysValue']['debug']['debug'])
                  VarDumper::dump(array('array_search'=>in_array((string)$results[$stage]['id'],$rootkeysarr),'id'=>$results[$stage]['id'],'retval'=>$retval,'rootkeysarr'=>$rootkeysarr));
           }
           //потомки потомков
           if ((int)$results[$stage]['childparentid']==$parentid && !empty($results[$stage]['childparentid'])){
               $childkeysarr[]=$results[$stage]['childid'];
               $parentid=(int)$results[$stage]['childid'];
                $retval[]=array('id'=>$results[$stage]['childid'],
                    'parentid'=>$results[$stage]['childparentid'],
                    'userid'=>$results[$stage]['childuserid'],
                    'bloguser'=>$results[$stage]['childbloguser'],
                    'create'=>$results[$stage]['childcreate'],
                    'blogtext'=>$results[$stage]['childblogtext'],
                    'sort'=>$results[$stage]['childsort'],
                    'level'=>$results[$stage]['childlevel']);
           }
           //VarDumper::dump(array('childkeysarr'=>$childkeysarr,));
           $stage++;
           return $this->buildBlogArray($results,$stage,$retval,$rootkeysarr,$childkeysarr,$results[$stage]['childparentid'],$parentid);
       } else {
            if ($GLOBALS['SysValue']['debug']['debug'])
               VarDumper::dump(array('buildBlogArray'=> $retval));
            return $retval;
       }
   }

}
