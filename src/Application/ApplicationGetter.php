<?php

declare(strict_types=1);

namespace kissj\Application;

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use kissj\Settings\Settings;
use Slim\App;

class ApplicationGetter
{
    public function getApp(
        string $envPath = __DIR__ . '/../../',
        string $envFilename = '.env',
        string $dbFullPath = __DIR__ . '/../db_dev.sqlite',
        string $tempPath = __DIR__ . '/../../temp'
    ): App {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions((new Settings())->getContainerDefinition(
            $envPath,
            $envFilename,
            $dbFullPath
        ));
        $containerBuilder->useAnnotations(true); // used in AbstractController
        if ($_ENV['DEBUG'] === 'false') {
            // TODO add autowired definitions into container to get more performace
            // https://php-di.org/doc/performances.html#optimizing-for-compilation
            $containerBuilder->enableCompilation($tempPath);
        }

        $container = $containerBuilder->build();
        $app       = Bridge::create($container);
        $app->setBasePath($_ENV['BASEPATH']);

        $app = (new Middleware())->addMiddlewaresInto($app);
        $app = (new Route())->addRoutesInto($app);

        return $app;
    }
}
