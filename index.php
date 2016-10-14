<?php
global $SysValue;
//подключаем конфиг файл
$GLOBALS['iniFileError']='Ошибка инициализации конфигурационного файла.';
$GLOBALS['mysqlConnectionError']='Ошибка соединения с базой даных.';
$GLOBALS['mysqlDBError']='Ошибка, отсутсвует база даных.';
$GLOBALS['symfonydebug']=true;
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

$request = Request::createFromGlobals();

require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/blogloader.php';
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/routes.php';
require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/routeprocessor.php';
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates/blogs.php';