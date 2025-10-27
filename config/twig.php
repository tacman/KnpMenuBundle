<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Knp\Menu\Twig\MenuExtension;
use Knp\Menu\Twig\MenuRuntimeExtension;
use Knp\Menu\Renderer\TwigRenderer;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $container->parameters()
        ->set('knp_menu.renderer.twig.options', []);

    $services->set('knp_menu.twig.extension', MenuExtension::class)
        ->tag('twig.extension');

    $services->set('knp_menu.twig.runtime', MenuRuntimeExtension::class)
        ->tag('twig.runtime')
        ->args([
            service('knp_menu.helper'),
            service('knp_menu.matcher'),
            service('knp_menu.manipulator'),
        ]);

    $services->set('knp_menu.renderer.twig', TwigRenderer::class)
        ->tag('knp_menu.renderer', ['alias' => 'twig'])
        ->args([
            service('twig'),
            param('knp_menu.renderer.twig.template'),
            service('knp_menu.matcher'),
            param('knp_menu.renderer.twig.options'),
        ]);
};
