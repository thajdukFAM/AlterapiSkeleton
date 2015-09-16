<?php

use Silex\Application;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use KinetiseSkeleton\Provider\ServiceProvider as KinetiseServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpKernel\Exception\HttpException;
use KinetiseSkeleton\Response\MessageResponse;
use Silex\Provider\MonologServiceProvider;
use KinetiseSkeleton\Doctrine\Logger as KinetiseDoctrineLogger;

AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation',
    APP_PATH . '/vendor/jms/serializer/src'
);

AnnotationRegistry::registerAutoloadNamespace(
    'Doctrine\ORM\Mapping',
    APP_PATH . '/vendor/doctrine/orm/lib'
);

$app = new Application();
$app['app.rootDir'] = APP_PATH;
$app['debug'] = APP_DEBUG;

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new KinetiseServiceProvider());
$app->register(new DoctrineOrmServiceProvider());
$app->register(new MonologServiceProvider());

// initialize serializer
$app['jms.serializer'] = $app->share(function () use ($app) {
    $builder = SerializerBuilder::create();
    $builder->addDefaultDeserializationVisitors();
    $builder->addDefaultSerializationVisitors();
    $builder->addDefaultHandlers();
    $builder->addDefaultListeners();
    $builder->setCacheDir($app['app.cacheDir']);
    $builder->addMetadataDir($app['app.cacheDir']);
    $builder->setDebug($app['debug']);

    return $builder->build();
});

$app['db.config'] = $app->extend('db.config', function($config, $app) {
    $config->setSQLLogger(
        new KinetiseDoctrineLogger($app['monolog'])
    );
    return $config;
});

if ($app['debug'] === true) {
    $app->register(new WebProfilerServiceProvider());
}

$app->error(function(\Exception $e, $code) use ($app) {
    if ($app['debug'] === true) {
        return;
    }

    $message = 'Oops, something goes wrong';

    if ($e instanceof HttpException) {
        $message = $e->getMessage();
    }

    return new MessageResponse($message, array(), $code, array(
        'Content-Type' => 'application/xml; charset=UTF-8'
    ));
});

$app->register(new ConfigServiceProvider(
    sprintf('%s/app/config/%s.json', $app['app.rootDir'], APP_ENV),
    array('app.rootDir' => $app['app.rootDir'])
));

return $app;