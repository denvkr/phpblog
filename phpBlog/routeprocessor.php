<?php
namespace phpBlog;
//require_once filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel;
use Symfony\Component\VarDumper\VarDumper;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;

class routeprocessor {
    //use phpBlog\Singleton;
    private $request;
    public function __construct(){
        //$request = Request::createFromGlobals();
        $this->request=$GLOBALS['request'];
        if ($GLOBALS['symfonydebug'])
            VarDumper::dump(array('request'=>$this->request));
        $requestStack = new RequestStack();
        $routes = include filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/phpBlog/routes.php';
        if ($GLOBALS['symfonydebug'])
            VarDumper::dump(array('routes'=>$routes));

        $context = new Routing\RequestContext;

        $context->fromRequest($this->request);
        if ($GLOBALS['symfonydebug'])
            VarDumper::dump(array('context'=>$context));

        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        if ($GLOBALS['symfonydebug'])
            VarDumper::dump(array('matcher'=>$matcher));
        //$controllerResolver = new Symfony\Component\HttpKernel\Controller\ControllerResolver();
        //$argumentResolver = new Symfony\Component\HttpKernel\Controller\ArgumentResolver();

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
            if (empty($this->request->getBaseUrl()))
                $url=$this->request->getPathInfo();
            else
                $url=$this->request->getBaseUrl();
            extract($matcher->match($url), EXTR_SKIP);
            //ob_start();
            //include sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates/%s.php', $_route);
            //$response = new Response(ob_get_clean());
            $this->request->attributes->add($matcher->match($url));
            if ($GLOBALS['symfonydebug'])
                VarDumper::dump(array('attributes->all'=>$this->request->attributes->all(),'security_conf'=>$this->getConfigTreeBuilder()));
            $response = call_user_func($this->request->attributes->get('_controller'), $this->request);
        } catch (Routing\Exception\ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (Exception $e) {
            $response = new Response('An error occurred '.$e, 500);
        }
        $response->prepare($this->request);
        return $response->send();
    }
    
    public function render_template(Request $request) {

        extract($request->attributes->all(), EXTR_SKIP);
        if (!isset($_route))
           $_route='blogs';
        if ($GLOBALS['symfonydebug'])
            VarDumper::dump(array('$_route'=>$_route,'include template'=>sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/'.$_route.'/%s.php', $_route)));

        ob_start();
        if ($_route=='blogs')
            include sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/templates/%s.php', $_route);
        else
            include sprintf(filter_input(INPUT_SERVER,'DOCUMENT_ROOT').'/'.$_route.'/%s.php', $_route);

        //return new \Symfony\Component\HttpFoundation\RedirectResponse('/'.$_route.'/'.$_route.'.php',301, ob_get_clean());
        return new Response(ob_get_clean());
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();
        $rootNode = $tb->root('security');

        $rootNode
            ->children()
                ->scalarNode('access_denied_url')->defaultNull()->example('/foo/error403')->end()
                ->enumNode('session_fixation_strategy')
                    ->values(array(SessionAuthenticationStrategy::NONE, SessionAuthenticationStrategy::MIGRATE, SessionAuthenticationStrategy::INVALIDATE))
                    ->defaultValue(SessionAuthenticationStrategy::MIGRATE)
                ->end()
                ->booleanNode('hide_user_not_found')->defaultTrue()->end()
                ->booleanNode('always_authenticate_before_granting')->defaultFalse()->end()
                ->booleanNode('erase_credentials')->defaultTrue()->end()
                ->arrayNode('access_decision_manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('strategy')
                            ->values(array(AccessDecisionManager::STRATEGY_AFFIRMATIVE, AccessDecisionManager::STRATEGY_CONSENSUS, AccessDecisionManager::STRATEGY_UNANIMOUS))
                            ->defaultValue(AccessDecisionManager::STRATEGY_AFFIRMATIVE)
                        ->end()
                        ->booleanNode('allow_if_all_abstain')->defaultFalse()->end()
                        ->booleanNode('allow_if_equal_granted_denied')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addAclSection($rootNode);
        $this->addEncodersSection($rootNode);
        //$this->addProvidersSection($rootNode);
        //$this->addFirewallsSection($rootNode, $this->factories);
        $this->addAccessControlSection($rootNode);
        $this->addRoleHierarchySection($rootNode);

        return $tb;
    }
    private function addAclSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('acl')
                    ->children()
                        ->scalarNode('connection')
                            ->defaultNull()
                            ->info('any name configured in doctrine.dbal section')
                        ->end()
                        ->arrayNode('cache')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('prefix')->defaultValue('sf2_acl_')->end()
                            ->end()
                        ->end()
                        ->scalarNode('provider')->end()
                        ->arrayNode('tables')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->defaultValue('acl_classes')->end()
                                ->scalarNode('entry')->defaultValue('acl_entries')->end()
                                ->scalarNode('object_identity')->defaultValue('acl_object_identities')->end()
                                ->scalarNode('object_identity_ancestors')->defaultValue('acl_object_identity_ancestors')->end()
                                ->scalarNode('security_identity')->defaultValue('acl_security_identities')->end()
                            ->end()
                        ->end()
                        ->arrayNode('voter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('allow_if_object_identity_unavailable')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addRoleHierarchySection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('role', 'role_hierarchy')
            ->children()
                ->arrayNode('role_hierarchy')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->performNoDeepMerging()
                        ->beforeNormalization()->ifString()->then(function ($v) { return array('value' => $v); })->end()
                        ->beforeNormalization()
                            ->ifTrue(function ($v) { return is_array($v) && isset($v['value']); })
                            ->then(function ($v) { return preg_split('/\s*,\s*/', $v['value']); })
                        ->end()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addAccessControlSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('rule', 'access_control')
            ->children()
                ->arrayNode('access_control')
                    ->cannotBeOverwritten()
                    ->prototype('array')
                        ->fixXmlConfig('ip')
                        ->fixXmlConfig('method')
                        ->children()
                            ->scalarNode('requires_channel')->defaultNull()->end()
                            ->scalarNode('path')
                                ->defaultNull()
                                ->info('use the urldecoded format')
                                ->example('^/path to resource/')
                            ->end()
                            ->scalarNode('host')->defaultNull()->end()
                            ->arrayNode('ips')
                                ->beforeNormalization()->ifString()->then(function ($v) { return array($v); })->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('methods')
                                ->beforeNormalization()->ifString()->then(function ($v) { return preg_split('/\s*,\s*/', $v); })->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('allow_if')->defaultNull()->end()
                        ->end()
                        ->fixXmlConfig('role')
                        ->children()
                            ->arrayNode('roles')
                                ->beforeNormalization()->ifString()->then(function ($v) { return preg_split('/\s*,\s*/', $v); })->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addFirewallsSection(ArrayNodeDefinition $rootNode, array $factories)
    {
        $firewallNodeBuilder = $rootNode
            ->fixXmlConfig('firewall')
            ->children()
                ->arrayNode('firewalls')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->disallowNewKeysInSubsequentConfigs()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
        ;

        $firewallNodeBuilder
            ->scalarNode('pattern')->end()
            ->scalarNode('host')->end()
            ->arrayNode('methods')
                ->beforeNormalization()->ifString()->then(function ($v) { return preg_split('/\s*,\s*/', $v); })->end()
                ->prototype('scalar')->end()
            ->end()
            ->booleanNode('security')->defaultTrue()->end()
            ->scalarNode('user_checker')
                ->defaultValue('security.user_checker')
                ->treatNullLike('security.user_checker')
                ->info('The UserChecker to use when authenticating users in this firewall.')
            ->end()
            ->scalarNode('request_matcher')->end()
            ->scalarNode('access_denied_url')->end()
            ->scalarNode('access_denied_handler')->end()
            ->scalarNode('entry_point')->end()
            ->scalarNode('provider')->end()
            ->booleanNode('stateless')->defaultFalse()->end()
            ->scalarNode('context')->cannotBeEmpty()->end()
            ->arrayNode('logout')
                ->treatTrueLike(array())
                ->canBeUnset()
                ->children()
                    ->scalarNode('csrf_parameter')->defaultValue('_csrf_token')->end()
                    ->scalarNode('csrf_token_generator')->cannotBeEmpty()->end()
                    ->scalarNode('csrf_token_id')->defaultValue('logout')->end()
                    ->scalarNode('path')->defaultValue('/logout')->end()
                    ->scalarNode('target')->defaultValue('/')->end()
                    ->scalarNode('success_handler')->end()
                    ->booleanNode('invalidate_session')->defaultTrue()->end()
                ->end()
                ->fixXmlConfig('delete_cookie')
                ->children()
                    ->arrayNode('delete_cookies')
                        ->beforeNormalization()
                            ->ifTrue(function ($v) { return is_array($v) && is_int(key($v)); })
                            ->then(function ($v) { return array_map(function ($v) { return array('name' => $v); }, $v); })
                        ->end()
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('path')->defaultNull()->end()
                                ->scalarNode('domain')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->fixXmlConfig('handler')
                ->children()
                    ->arrayNode('handlers')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('anonymous')
                ->canBeUnset()
                ->children()
                    ->scalarNode('secret')->defaultValue(uniqid('', true))->end()
                ->end()
            ->end()
            ->arrayNode('switch_user')
                ->canBeUnset()
                ->children()
                    ->scalarNode('provider')->end()
                    ->scalarNode('parameter')->defaultValue('_switch_user')->end()
                    ->scalarNode('role')->defaultValue('ROLE_ALLOWED_TO_SWITCH')->end()
                ->end()
            ->end()
        ;

        $abstractFactoryKeys = array();
        foreach ($factories as $factoriesAtPosition) {
            foreach ($factoriesAtPosition as $factory) {
                $name = str_replace('-', '_', $factory->getKey());
                $factoryNode = $firewallNodeBuilder->arrayNode($name)
                    ->canBeUnset()
                ;

                if ($factory instanceof AbstractFactory) {
                    $abstractFactoryKeys[] = $name;
                }

                $factory->addConfiguration($factoryNode);
            }
        }

        // check for unreachable check paths
        $firewallNodeBuilder
            ->end()
            ->validate()
                ->ifTrue(function ($v) {
                    return true === $v['security'] && isset($v['pattern']) && !isset($v['request_matcher']);
                })
                ->then(function ($firewall) use ($abstractFactoryKeys) {
                    foreach ($abstractFactoryKeys as $k) {
                        if (!isset($firewall[$k]['check_path'])) {
                            continue;
                        }

                        if (false !== strpos($firewall[$k]['check_path'], '/') && !preg_match('#'.$firewall['pattern'].'#', $firewall[$k]['check_path'])) {
                            throw new \LogicException(sprintf('The check_path "%s" for login method "%s" is not matched by the firewall pattern "%s".', $firewall[$k]['check_path'], $k, $firewall['pattern']));
                        }
                    }

                    return $firewall;
                })
            ->end()
        ;
    }

    private function addProvidersSection(ArrayNodeDefinition $rootNode)
    {
        $providerNodeBuilder = $rootNode
            ->fixXmlConfig('provider')
            ->children()
                ->arrayNode('providers')
                    ->example(array(
                        'my_memory_provider' => array(
                            'memory' => array(
                                'users' => array(
                                    'foo' => array('password' => 'foo', 'roles' => 'ROLE_USER'),
                                    'bar' => array('password' => 'bar', 'roles' => '[ROLE_USER, ROLE_ADMIN]'),
                                ),
                            ),
                        ),
                        'my_entity_provider' => array('entity' => array('class' => 'SecurityBundle:User', 'property' => 'username')),
                    ))
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
        ;

        $providerNodeBuilder
            ->children()
                ->scalarNode('id')->end()
                ->arrayNode('chain')
                    ->fixXmlConfig('provider')
                    ->children()
                        ->arrayNode('providers')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(function ($v) { return preg_split('/\s*,\s*/', $v); })
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        foreach ($this->userProviderFactories as $factory) {
            $name = str_replace('-', '_', $factory->getKey());
            $factoryNode = $providerNodeBuilder->children()->arrayNode($name)->canBeUnset();

            $factory->addConfiguration($factoryNode);
        }

        $providerNodeBuilder
            ->validate()
                ->ifTrue(function ($v) {return count($v) > 1;})
                ->thenInvalid('You cannot set multiple provider types for the same provider')
            ->end()
            ->validate()
                ->ifTrue(function ($v) {return count($v) === 0;})
                ->thenInvalid('You must set a provider definition for the provider.')
            ->end()
        ;
    }

    private function addEncodersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('encoder')
            ->children()
                ->arrayNode('encoders')
                    ->example(array(
                        'Acme\DemoBundle\Entity\User1' => 'sha512',
                        'Acme\DemoBundle\Entity\User2' => array(
                            'algorithm' => 'sha512',
                            'encode_as_base64' => 'true',
                            'iterations' => 5000,
                        ),
                    ))
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('class')
                    ->prototype('array')
                        ->canBeUnset()
                        ->performNoDeepMerging()
                        ->beforeNormalization()->ifString()->then(function ($v) { return array('algorithm' => $v); })->end()
                        ->children()
                            ->scalarNode('algorithm')->cannotBeEmpty()->end()
                            ->scalarNode('hash_algorithm')->info('Name of hashing algorithm for PBKDF2 (i.e. sha256, sha512, etc..) See hash_algos() for a list of supported algorithms.')->defaultValue('sha512')->end()
                            ->scalarNode('key_length')->defaultValue(40)->end()
                            ->booleanNode('ignore_case')->defaultFalse()->end()
                            ->booleanNode('encode_as_base64')->defaultTrue()->end()
                            ->scalarNode('iterations')->defaultValue(5000)->end()
                            ->integerNode('cost')
                                ->min(4)
                                ->max(31)
                                ->defaultValue(13)
                            ->end()
                            ->scalarNode('id')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
