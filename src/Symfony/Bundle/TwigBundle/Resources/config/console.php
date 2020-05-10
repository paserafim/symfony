<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bundle\TwigBundle\Command\LintCommand;

return static function (ContainerConfigurator $container) {
    $container->services()

    ->set('twig.command.debug', DebugCommand::class)
    ->args([
        ref('twig'),
        param('kernel.project_dir'),
        param('kernel.bundles_metadata'),
        param('twig.default_path'),
        ref('debug.file_link_formatter')->nullOnInvalid(),
    ])
    ->tag('console.command', ['command' => 'debug:twig'])

    ->set('twig.command.lint', LintCommand::class)
    ->args([ref('twig')])
    ->tag('console.command', ['command' => 'lint:twig'])
    ;
};
