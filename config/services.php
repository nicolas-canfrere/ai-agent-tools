<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Agent\AgentBuilder;
use App\Agent\AgentBuilderInterface;

return function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->bind('$apiKey', env('OPENAI_API_KEY'))
        ->autowire()
        ->autoconfigure()
    ;
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    $services->set(AgentBuilderInterface::class)
        ->class(AgentBuilder::class);
};
