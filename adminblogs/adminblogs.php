<?php
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/vendor/autoload.php';
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/menubuilder.php';
use phpBlog\menubuilder;
use phpBlog\blogloader as blogloader;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;

require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/blogloader.php';

//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/index.php';
$request = Request::createFromGlobals();
$request->create(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/adminblogs','GET');
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/routeprocessor.php';

$routes = new RouteCollection();
$route=new Route('/adminblogs',array('_controller' =>'phpBlog\routeprocessor::render_template'));
$routes->add('adminblogs',$route);

VarDumper::dump($routes);

$context = new RequestContext;
$context->fromRequest($request);
VarDumper::dump($context);

$matcher = new UrlMatcher($routes, $context);

if (empty($request->getBaseUrl()))
    $url=$request->getPathInfo();
else
    $url=$request->getBaseUrl();
extract($matcher->match($url), EXTR_SKIP);
//ob_start();
//include sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates/%s.php', $_route);
//$response = new Response(ob_get_clean());
$request->attributes->add($matcher->match($url));

echo 'Админка блога';

//if ($GLOBALS['SysValue']['debug']['debug']){
    VarDumper::dump($request,'request');
//}

if ($request->get('_route')=='adminblogs'){
    $PathiniFile = filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/config/config.ini';

    //если конфиг файл существует, 
    //все остальное имеет смысл 
    if (file_exists($PathiniFile)) {
    //инициализируем шаблонизатор
    \Twig_Autoloader::register();
    //echo filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates';
    $loader = new Twig_Loader_Filesystem(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/adminblogs');
    if ($GLOBALS['SysValue']['debug']['debug'])
        VarDumper::dump(array('loader'=>$loader->getPaths()));

    $twig = new Twig_Environment($loader, array(
        'cache' => false,//'compilation_cache',
        'debug' => true,
        'auto_reload' => true
    ));

    //инициализируем всю работу с базой данных
    $blogloader=new blogloader();

    //получаем кол-во попыток логина
    $attempts=$blogloader->getSessionInfo();

    //кол-во посещений страниц пользователями
    $visitorCount=$blogloader->get_total_sessions_amount();

    if ($GLOBALS['SysValue']['debug']['debug'])
        VarDumper::dump(array('blogloader'=>$blogloader,'request'=>$request,'routes'=>$routes));

    //подключаем шаблон        
    $twig->clearTemplateCache();
    $template = $twig->loadTemplate('adminblogs.twig');

    //knpmenu
    $menubuilder=new menubuilder();
    
    echo $template->render(array('knpmenu'=>$menubuilder->mainMenu($twig,array()),
                                 'num_cnt_attempt'=>$attempts,
                                 'bootstrap_theme'=>'bootstrap-theme-slate',
                                 'visitor_count'=>$visitorCount,
                                 'host'=>$request->getClientIp()));

    } else
        echo $GLOBALS['iniFileError'];    

}

?>
