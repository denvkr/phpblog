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
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/blogloader.php';

//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/index.php';
$request = Request::createFromGlobals();
$request->create(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/adminblogs','GET');
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/routeprocessor.php';
if ($request->get('_route')=='adminblogs'){
    //echo 'Админка блога';
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
