<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpBlog;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\TwigRenderer;
use Knp\Menu\Matcher\Matcher;
/**
 * Description of menubuilder
 *
 * @author dkrasavin
 */
class menubuilder implements ContainerAwareInterface{
    use ContainerAwareTrait;

    public function mainMenu(\Twig_Environment $twig,array $options=array())
    {
        $MenuFactory=new MenuFactory();
        $options=array('depth'=>1);
        $Matcher=new Matcher();
        $munulocal=$MenuFactory->createItem('root');
        $munulocal->addChild('Home', array('route' => 'blogs','label'=>'Home','uri'=>'/blogs'))->setAttribute('class','navbar-brand');
        $munulocal->addChild('Admin\'ka', array('route' => 'adminblogs','label'=>'Admin\'ka','uri'=>'/adminblogs','class'=>'navbar-brand'))->setAttribute('class','navbar-brand');

        $TwigRenderer=new TwigRenderer($twig,'knp_menu.html.twig',$Matcher,array('depth'=>1));
        $TwigRendererhtml=$TwigRenderer->render($munulocal);
        return $TwigRendererhtml;
    }
}