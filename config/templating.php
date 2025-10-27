<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Knp\Bundle\MenuBundle\Templating\Helper\MenuHelper;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $container->parameters()
        ->set('knp_menu.templating.helper.class', MenuHelper::class);

    $services->set('knp_menu.templating.helper')
        ->class(param('knp_menu.templating.helper.class'))
        ->tag('templating.helper', ['alias' => 'knp_menu'])
        ->args([
            service('knp_menu.helper'),
            service('knp_menu.matcher'),
            service('knp_menu.manipulator'),
        ]);
};
