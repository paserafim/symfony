<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;

return static function (ContainerConfigurator $container) {
    $container->services()

    ->set('twig.extension.form', FormExtension::class)

    ->set('twig.form.engine', TwigRendererEngine::class)
    ->args([param('twig.form.resources'), ref('twig')])

    ->set('twig.form.renderer', FormRenderer::class)
    ->args([ref('twig.form.engine'), ref('security.csrf.token_manager')->nullOnInvalid()])
    ->tag('twig.runtime')
    ;
};
