<?php
//подключаем необходимые библиотеки
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/vendor/autoload.php';
//вариант работы через кастомизированную логику 
//с использованием компонентов symfony инициализации конец

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Session\Session;

$request = Request::createFromGlobals();
if (!$request->hasSession()) {
    $request->setSession(new Session);
    $request->getSession()->start();
}

$parameters=@json_decode($request->getContent(),true);
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/blogloader.php';
use phpBlog\blogloader;
//var_dump(array($parameters));
$blogloader=new blogloader();

$memcache = new \Memcache;
$memcache->connect('127.0.0.1', 11211) or exit("Невозможно подключиться к серверу Memcached");
if (isset($parameters['type'])){
    //отрабатываем логин пользователя
    if ($parameters['type']=="user_authentication"){
        if ($memcache->get($parameters['user'])!==false) {
            $userinfo=$memcache->get($parameters['user']);
            if (intval($userinfo[3])===1 && $userinfo[2]===$parameters['password']){
               $result=array('hasuser'=>1);
                if ($request->hasSession()) {
                    //пишем обновленные данные в базу
                    if (!$request->getSession()->has('login') && !$request->getSession()->has('login_id')){
                        $request->getSession()->set('login',$userinfo[0]);
                        $request->getSession()->set('login_id',$userinfo[1]);
                    }
                } else {
                    $result=array('hasuser'=>0);
                }       
            } else{
                $result=$blogloader->doctrine_get_user($parameters['user'],$parameters['password']);
                $memcache->set($parameters['user'], array($parameters['user'],$result['id'],$parameters['password'],$result['hasuser']), false, 0);
            }
        } else {
            $result=$blogloader->doctrine_get_user($parameters['user'],$parameters['password']);
            $memcache->set($parameters['user'], array($parameters['user'],$result['id'],$parameters['password'],$result['hasuser']), false, 0);
        }
        if ($GLOBALS['SysValue']['debug']['debug'])
            VarDumper::dump(array('memcacheajax'=>$memcache->get($parameters['user'],$request->getSession()),'resultajax'=>$result));
        // Формируем результат
        echo json_encode($result);//json_encode($request->attributes->all());

    }
    //отрабатываем выход пользователя
    if ($parameters['type']=="user_dislogged"){

        if ($request->hasSession()) {
            //if ($request->getSession()->has('login') && $request->getSession()->has('login_id')){
            //    if ($memcache->get($request->getSession()->has('login'))!==false) {
            //       $memcache->delete($request->getSession()->has('login'));
            //    }
            //}
            $request->getSession()->invalidate();
            $result=array('dislogged'=>1);                    
        } else {
            $result=array('dislogged'=>0);                    
        }

        //VarDumper::dump(array($memcache->get($parameters['user'],$request->getSession())));
        // Формируем результат
        echo json_encode($result);//json_encode($request->attributes->all());

    }

    //отрабатываем добавление блога
    if ($parameters['type']=="addblog") {
        if ($request->hasSession()) {
            //пишем обновленные данные в базу
            if ($request->getSession()->has('login') && $request->getSession()->has('login_id')){
                $result=$blogloader->doctrine_add_post($parameters['blogparentid'],$request->getSession()->get('login'),
                                                       $request->getSession()->get('login_id'),
                                                       $parameters['blogtext'],
                                                       $parameters['mainroot']);
                $result=array('blogwasadded'=>$result); 
            } else {
                $result=array('blogwasadded'=>0);
            }
        } else {
            $result=array('blogwasadded'=>0);
        }
        //VarDumper::dump(array($memcache->get($parameters['user'],$request->getSession())));
        // Формируем результат
        echo json_encode($result);//json_encode($request->attributes->all());
    }
}
?>