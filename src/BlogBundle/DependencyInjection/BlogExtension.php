<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BlogBundle\DependencyInjection;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Description of IpoetryExtension
 *
 * @author d.krasavin
 */
class BlogExtension extends Extension {
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        //var_dump($config['UserEmailAuthUrl']);
        //$container->setParameter('ipoetry.UserEmailAuthUrl', $config['UserEmailAuthUrl']);
        
/*
        $loader = new YamlFileLoader(
                    $container,
                    new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('ipoetry_config_dev.yml');
 * 
 */
    }
    public function getAlias() {
        //parent::getAlias();
        return 'blog';
    }    
}
