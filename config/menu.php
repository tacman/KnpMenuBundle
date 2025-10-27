<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Knp\Menu\MenuFactory;
use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Provider\MenuProviderInterface;
use Knp\Menu\Provider\ChainProvider;
use Knp\Menu\Provider\LazyProvider;
use Knp\Bundle\MenuBundle\Provider\BuilderAliasProvider;
use Knp\Menu\Renderer\PsrProvider;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Voter\CallbackVoter;
use Knp\Menu\Matcher\Voter\RouteVoter;
use Knp\Menu\Util\MenuManipulator;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\MatcherInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $container->parameters()
        ->set('knp_menu.renderer.list.options', []);

    $services->set('knp_menu.factory', MenuFactory::class)
        ->public();

    $services->set('knp_menu.factory_extension.routing', RoutingExtension::class)
        ->args([
            service('router'),
        ])
        ->tag('knp_menu.factory_extension');

    $services->set('knp_menu.helper', Helper::class)
        ->args([
            service('knp_menu.renderer_provider'),
            service('knp_menu.menu_provider'),
            service('knp_menu.manipulator'),
            service('knp_menu.matcher'),
        ]);

    $services->set('knp_menu.matcher', Matcher::class)
        ->public()
        ->args([
            tagged_iterator('knp_menu.voter'),
        ])
        ->tag('kernel.reset', ['method' => 'clear']);

    $services->alias(MenuProviderInterface::class, 'knp_menu.menu_provider');

    $services->set('knp_menu.menu_provider.chain', ChainProvider::class)
        ->args([
            tagged_iterator('knp_menu.provider'),
        ]);

    $services->set('knp_menu.menu_provider.lazy', LazyProvider::class)
        ->args([[]])
        ->tag('knp_menu.provider');

    $services->set('knp_menu.menu_provider.builder_alias', BuilderAliasProvider::class)
        ->args([
            service('kernel'),
            service('service_container'),
            service('knp_menu.factory'),
        ]);

    $services->set('knp_menu.renderer_provider', PsrProvider::class)
        ->args([
            tagged_locator('knp_menu.renderer', 'alias'),
            param('knp_menu.default_renderer'),
        ]);

    $services->set('knp_menu.renderer.list', ListRenderer::class)
        ->tag('knp_menu.renderer', ['alias' => 'list'])
        ->args([
            service('knp_menu.matcher'),
            param('knp_menu.renderer.list.options'),
            param('kernel.charset'),
        ]);

    $services->set('knp_menu.voter.callback', CallbackVoter::class)
        ->tag('knp_menu.voter');

    $services->set('knp_menu.voter.router', RouteVoter::class)
        ->args([
            service('request_stack'),
        ])
        ->tag('knp_menu.voter');

    $services->set('knp_menu.manipulator', MenuManipulator::class);

    // Autowiring aliases
    $services->alias(FactoryInterface::class, 'knp_menu.factory');
    $services->alias(MatcherInterface::class, 'knp_menu.matcher');
    $services->alias(MenuManipulator::class, 'knp_menu.manipulator');
};
