<?php

use Symfony\Component\Routing;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();
$routes->add('blogs', new Routing\Route('/',array('_controller' => 'render_template')));//blogsController::indexAction
$routes->add('blog', new Routing\Route('/blog/{user}_{item}',array('_controller' => 'phpblog\routeprocessor::render_template')));//blogController::indexAction
$routes->add('adminblogs', new Routing\Route('/adminblogs',array('_controller' => 'phpblog\routeprocessor::render_template')));//adminblogsController::indexAction

return $routes;