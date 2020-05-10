<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Component\Mailer\EventListener\MessageListener;

return static function (ContainerConfigurator $container) {
    $container->services()

    ->set('twig.mailer.message_listener', MessageListener::class)
    ->args([null, ref('twig.mime_body_renderer')])
    ->tag('kernel.event_subscriber')

    ->set('twig.mime_body_renderer', BodyRenderer::class)
    ->args([ref('twig')])
    ;
};
