<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Symfony\Bridge\Twig\ErrorRenderer\TwigErrorRenderer;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\CodeExtension;
use Symfony\Bridge\Twig\Extension\ExpressionExtension;
use Symfony\Bridge\Twig\Extension\HttpFoundationExtension;
use Symfony\Bridge\Twig\Extension\HttpKernelExtension;
use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Bridge\Twig\Extension\ProfilerExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\StopwatchExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\WebLinkExtension;
use Symfony\Bridge\Twig\Extension\WorkflowExtension;
use Symfony\Bridge\Twig\Extension\YamlExtension;
use Symfony\Bridge\Twig\Translation\TwigExtractor;
use Symfony\Bundle\TwigBundle\CacheWarmer\TemplateCacheWarmer;
use Symfony\Bundle\TwigBundle\DependencyInjection\Configurator\EnvironmentConfigurator;
use Symfony\Bundle\TwigBundle\TemplateIterator;
use Twig\Cache\FilesystemCache;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Extension\DebugExtension;
use Twig\Extension\EscaperExtension;
use Twig\Extension\OptimizerExtension;
use Twig\Extension\StagingExtension;
use Twig\ExtensionSet;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Profiler\Profile;
use Twig\RuntimeLoader\ContainerRuntimeLoader;
use Twig\Template;
use Twig\TemplateWrapper;

return static function (ContainerConfigurator $container) {
    $container->services()

    ->set('twig', Environment::class)
    ->public()
    ->args([ref('twig.loader'), abstract_arg('Twig options')])
    ->call('addGlobal', ['app', ref('twig.app_variable')])
    ->call('addRuntimeLoader', [ref('twig.runtime_loader')])
    ->configurator([ref('twig.configurator.environment'), 'configure'])
    ->tag('container.preload', ['class' => FilesystemCache::class])
    ->tag('container.preload', ['class' => CoreExtension::class])
    ->tag('container.preload', ['class' => EscaperExtension::class])
    ->tag('container.preload', ['class' => OptimizerExtension::class])
    ->tag('container.preload', ['class' => StagingExtension::class])
    ->tag('container.preload', ['class' => ExtensionSet::class])
    ->tag('container.preload', ['class' => Template::class])
    ->tag('container.preload', ['class' => TemplateWrapper::class])

    ->alias('Twig_Environment', 'twig')
    ->alias(Environment::class, 'twig')

    ->set('twig.app_variable', AppVariable::class)
    ->call('setEnvironment', [param('kernel.environment')])
    ->call('setDebug', [param('kernel.debug')])
    ->call('setTokenStorage', [ref('security.token_storage')->ignoreOnInvalid()])
    ->call('setRequestStack', [ref('request_stack')->ignoreOnInvalid()])

    ->set('twig.template_iterator', TemplateIterator::class)
    ->args([ref('kernel'), abstract_arg('Twig paths'), param('twig.default_path')])

    ->set('twig.template_cache_warmer', TemplateCacheWarmer::class)
    ->args([ref(ContainerInterface::class, ref('twig.template_iterator')])
    ->tag('kernel.cache_warmer')
    ->tag('container.service_subscriber', ['id' => 'twig'])

    ->set('twig.loader.native_filesystem', FilesystemLoader::class)
    ->args([[], param('kernel.project_dir')])
    ->tag('twig.loader')

    ->set('twig.loader.chain', ChainLoader::class)

    ->set('twig.extension.profiler', ProfilerExtension::class)
    ->args([ref('twig.profile'), ref('debug.stopwatch')->ignoreOnInvalid()])

    ->set('twig.profile', Profile::class)

    ->set('data_collector.twig', TwigDataCollector::class)
    ->args([ref('twig.profile'), ref('twig')])
    ->tag('data_collector', ['template' => '@WebProfiler/Collector/twig.html.twig', 'id' => 'twig', 'priority' => 257])

    ->set('twig.extension.trans', TranslationExtension::class)
    ->args([ref('translation')->nullOnInvalid()])
    ->tag('twig.extension')

    ->set('twig.extension.assets', AssetExtension::class)
    ->args([ref('assets.package')])

    ->set('twig.extension.code', CodeExtension::class)
    ->args([ref('debug.file_link_formatter')->ignoreOnInvalid(), param('kernel.project_dir'), param('kernel.charset')])
    ->tag('twig.extension')

    ->set('twig.extension.routing', RoutingExtension::class)
    ->args([ref('router')])

    ->set('twig.extension.yaml', YamlExtension::class)

    ->set('twig.extension.debug.stopwatch', StopwatchExtension::class)
    ->args([ref('debug.stopwatch')->ignoreOnInvalid(), param('kernel.debug')])

    ->set('twig.extension.expression', ExpressionExtension::class)

    ->set('twig.extension.httpkernel', HttpKernelExtension::class)

    ->set('twig.runtime.httpkernel', HttpKernelRuntime::class)
    ->args([ref('fragment.handler')])

    ->set('twig.extension.httpfoundation', HttpFoundationExtension::class)
    ->args([ref('url_helper')])

    ->set('twig.extension.debug', DebugExtension::class)

    ->set('twig.extension.weblink', WebLinkExtension::class)
    ->args([ref('request_stack')])

    ->set('twig.translation.extractor', TwigExtractor::class)
    ->args([ref('twig')])
    ->tag('translation.extractor', ['alias' => 'twig'])

    ->set('workflow.twig_extension', WorkflowExtension::class)
    ->args([ref('workflow.registry')])

    ->set('twig.configurator.environment', EnvironmentConfigurator::class)
    ->args([
        abstract_arg('date format, set in TwigExtension'),
        abstract_arg('interval format, set in TwigExtension'),
        abstract_arg('timezone, set in TwigExtension'),
        abstract_arg('decimals, set in TwigExtension'),
        abstract_arg('decimal point, set in TwigExtension'),
        abstract_arg('thousands separator, set in TwigExtension'),
    ])

    ->set('twig.runtime_loader', ContainerRuntimeLoader::class)
    ->args([abstract_arg('runtime locator')])

    ->set('twig.error_renderer.html', TwigErrorRenderer::class)
    ->decorate('error_renderer.html')
    ->args([
        ref('twig'),
        ref('twig.error_renderer.html.inner'),
        service(TwigErrorRenderer::class)
        ->factory([TwigErrorRenderer::class, 'isDebug'])
        ->args([ref('request_stack'), param('kernel.debug')]),
    ])

    ;
};
