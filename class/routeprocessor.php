<?php
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel;
use Symfony\Component\VarDumper\VarDumper;

//$request = Request::createFromGlobals();
if ($GLOBALS['symfonydebug'])
    VarDumper::dump(array('request'=>$request));
$requestStack = new RequestStack();
$routes = include filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/class/routes.php';
if ($GLOBALS['symfonydebug'])
    VarDumper::dump(array('routes'=>$routes));

$context = new Routing\RequestContext;

$context->fromRequest($request);
if ($GLOBALS['symfonydebug'])
    VarDumper::dump(array('context'=>$context));

$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
if ($GLOBALS['symfonydebug'])
    VarDumper::dump(array('matcher'=>$matcher));
$controllerResolver = new Symfony\Component\HttpKernel\Controller\ControllerResolver();
$argumentResolver = new Symfony\Component\HttpKernel\Controller\ArgumentResolver();

$dispatcher = new EventDispatcher();

$errorHandler = function (Symfony\Component\Debug\Exception\FlattenException $exception) {
    $msg = 'Ошибка ('.$exception->getMessage().')';

    return new Response($msg, $exception->getStatusCode());
};
//добавляем обработчик переопределяемых событий
$dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher, $requestStack));
$dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener($errorHandler));
if ($GLOBALS['symfonydebug'])
    VarDumper::dump(array('$dispatcher'=>$dispatcher));

try {
    if (empty($request->getBaseUrl()))
        $url=$request->getPathInfo();
    else
        $url=$request->getBaseUrl();
    extract($matcher->match($url), EXTR_SKIP);
    //ob_start();
    //include sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates/%s.php', $_route);
    //$response = new Response(ob_get_clean());
    $request->attributes->add($matcher->match($url));
    if ($GLOBALS['symfonydebug'])
        VarDumper::dump(array('attributes->all'=>$request->attributes->all()));
    $response = call_user_func($request->attributes->get('_controller'), $request);
} catch (Routing\Exception\ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred '.$e, 500);
}
$response->send();

function render_template(Request $request) {

    extract($request->attributes->all(), EXTR_SKIP);
    if (!isset($_route))
       $_route='blogs';
    if ($GLOBALS['symfonydebug'])
        VarDumper::dump(array('$_route'=>$_route,'include template'=>sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/'.$_route.'/%s.php', $_route)));

    ob_start();
    if ($_route=='blogs')
        include sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates/%s.php', $_route);
    if ($_route!='blogs')
        include sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/'.$_route.'/%s.php', $_route);

    //return new \Symfony\Component\HttpFoundation\RedirectResponse('/'.$_route.'/'.$_route.'.php',301, ob_get_clean());
    return new Response(ob_get_clean());
}