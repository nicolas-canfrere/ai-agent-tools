<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Agent\Context;
use App\Agent\ContextInterface;
use App\Agent\TraceableContext;

return function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->bind('$apiKey', env('OPENAI_API_KEY'))
        ->autowire()
        ->autoconfigure()
    ;
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    $services->set(ContextInterface::class)
        ->class(Context::class);

    $services->set(TraceableContext::class)
        ->decorate(ContextInterface::class)
        ->arg('$decorated', service('.inner'));
};
