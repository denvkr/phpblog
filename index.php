<?php
global $SysValue;
//подключаем конфиг файл
$GLOBALS['iniFileError']='Ошибка инициализации конфигурационного файла.';
$GLOBALS['mysqlConnectionError']='Ошибка соединения с базой даных.';
$GLOBALS['mysqlDBError']='Ошибка, отсутсвует база даных.';
$GLOBALS['symfonydebug']=false;
//вариант работы через логику symfony начало инициализации
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/app/autoload.php';
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/app/AppCache.php';
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/app/AppKernel.php';
//вариант работы через логику symfony конец инициализации

//вариант работы через кастомизированную логику 
//с использованием компонентов symfony инициализации начало
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/vendor/autoload.php';

//вариант работы через кастомизированную логику 
//с использованием компонентов symfony инициализации конец

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Session\Session;
use Composer\Autoload\ClassLoader;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration;

$request = Request::createFromGlobals();
$GLOBALS['request']=$request;

if (!$request->hasSession()) {
    $request->setSession(new Session);
    $request->getSession()->start();
}
if (!class_exists("Memcache"))  exit("Memcached не установлен");
$GLOBALS['memcache'] = new \Memcache;
$GLOBALS['memcache']->connect('127.0.0.1', 11211) or exit("Невозможно подключиться к серверу Memcached");
 
$version = $GLOBALS['memcache']->getVersion();
if ($GLOBALS['symfonydebug'])
    VarDumper::dump(array('версия memcache'=>$version));
 
//$autoloader = new \phpBlog\autoloader();
 
// регистрируем наш автозагрузчик
//spl_autoload_register(array($autoloader, 'autoload'));
$ClassLoader=ComposerAutoloaderInite4e1d7de7cb78e2b2d34b94135940217::getLoader();
$classmap=array('phpBlog\blogloader'=>filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/blogloader.php',
                'phpBlog\routeprocessor'=>filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/routeprocessor.php',
                'phpBlog\parseini'=>filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/parseini.php',
                'phpBlog\dbroutine'=>filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/dbroutine.php',
                'phpBlog\phpBlogUser'=>filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/phpBlogUser.php',);

$ClassLoader->addClassMap($classmap);
//$ClassLoader->add('blogloader', filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/blogloader.php');
//$ClassLoader->add('routeprocessor',filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/routeprocessor.php');
//$ClassLoader->register();
//$ClassLoader->setUseIncludePath(true);

//var_dump(spl_autoload_functions());

//$autoloader->registerClass('blogloader', filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/blogloader.php');
//$autoloader->registerClass('routeprocessor', filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/routeprocessor.php');

$blogloader=new phpBlog\blogloader();
$routeprocessor=new phpBlog\routeprocessor();
$GLOBALS['routeprocessor']=$routeprocessor;
//var_dump(spl_autoload_functions());
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/blogloader.php';
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/routes.php';
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/routeprocessor.php';
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates/blogs.php';
