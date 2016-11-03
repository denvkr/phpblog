<?php
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/menubuilder.php';
use phpBlog\ajaxroutine;
use phpBlog\menubuilder;
use phpBlog\blogloader as blogloader;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\Role;

use phpBlog\phpBlogUser;
//$request = Request::create('http://phpblog/');

$PathiniFile = filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/config/config.ini';

//если конфиг файл существует, 
//все остальное имеет смысл 
if (file_exists($PathiniFile)) {
//инициализируем шаблонизатор
\Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array(
    'cache' => false,//'compilation_cache',
    'debug' => true,
    'auto_reload' => true
));

//инициализируем всю работу с базой данных
$blogloader=new blogloader();

//получаем кол-во попыток логина
$attempts='';//$blogloader->getSessionInfo();

//кол-во посещений страниц пользователями
$visitorCount=$blogloader->get_total_sessions_amount();

if ($GLOBALS['SysValue']['debug']['debug'])
    VarDumper::dump(array('blogloader'=>$blogloader,'request'=>$request,'routes'=>$routes));

//подключаем шаблон        
$twig->clearTemplateCache();
$template = $twig->loadTemplate('index.twig');

//knpmenu сайта
$menubuilder=new menubuilder();

//выясняем не залогинен ли уже участнки
if ($request->hasSession())
    if ($request->getSession()->has('login') && $request->getSession()->has('login_id')){
       $userinfo='Вы '.$request->getSession()->get('login');
       $authenticationbtnval='Выйти';
       $askmemcache=$GLOBALS['memcache']->get($request->getSession()->get('login'));
       $userlogged=true;
       /*
        * используем компоненты Symfony Security создаем токен 
        */
        /**
         * Constructor.
         *
         * @param string|object            $user        The username (like a nickname, email address, etc.), or a UserInterface instance or an object implementing a __toString method
         * @param string                   $credentials This usually is the password of the user
         * @param string                   $providerKey The provider key
         * @param RoleInterface[]|string[] $roles       An array of roles
         *
         * @throws \InvalidArgumentException
         */
       $phpBlogUser= new phpBlogUser();
       $role = new Role('ROLE_USER');
       $UsernamePasswordToken=new UsernamePasswordToken( $phpBlogUser,$request->getSession()->get('login_id'),'session',array($role->getRole()));
       //$UsernamePasswordToken->setUser($request->getSession()->get('login_id'));
       //$UsernamePasswordToken->setAttribute('login', $request->getSession()->get('login'));
       //$UsernamePasswordToken->setAttribute('login_id', $request->getSession()->get('login_id'));
       
    } else {
        $userinfo='';
        $authenticationbtnval='Войти';
        $askmemcache='';
        $userlogged=false;
        $UsernamePasswordToken=null;
    }
//получаем блоги
$blogs=$blogloader->doctrine_get_AllBlogs();
$blogssort=$blogloader->doctrine_get_AllBlogsSort();

if ($GLOBALS['SysValue']['debug']['debug'])
    VarDumper::dump(array('menu'=>$menu,'TwigRenderer'=>$TwigRenderer,
                          'TwigRendererhtml'=>$TwigRendererhtml,
                          'menubuilder'=>$menubuilder,
                          'Container'=>$Container,
                          'whoAreYou'=>$whoAreYou,
                          'memcache'=>$askmemcache,
                          'session'=>$request->hasSession(),
                          'blogs'=>$blogs,
                          'blogssort'=>$blogssort,
                          'UsernamePasswordToken'=>$UsernamePasswordToken,
                          //'phpBlogUser'=>$phpBlogUser->getUsername()
                         ));

echo $template->render(array('knpmenu'=>$menubuilder->mainMenu($twig,array()),
                             'num_cnt_attempt'=>$attempts,
                             'bootstrap_theme'=>'bootstrap-theme-slate',
                             'visitor_count'=>$visitorCount,
                             'host'=>$request->getClientIp(),
                             'ajaxroutine'=>'/phpBlog/ajax/blogsajax.php',
                             'userinfo'=>$userinfo,
                             'blogs'=>$blogssort,
                             'authenticationbtnval'=>$authenticationbtnval,
                             'userlogged'=>$userlogged));
} else
    echo $GLOBALS['iniFileError'];    

?>