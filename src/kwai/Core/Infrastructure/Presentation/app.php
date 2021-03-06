<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure;

use Cake\Datasource\ConnectionManager;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\LoggerDependency;
use Kwai\Core\Infrastructure\Dependencies\MailerDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use League\Container\Container;
use Slim\App;
use Slim\Factory\AppFactory;

/**
 * Creates a Slim application.
 *
 * @param string $basePath
 * @return App
 */
function createApplication(string $basePath = '/api')
{
    $container = new Container();
    $container->defaultToShared();
    AppFactory::setContainer($container);

    $container->add('settings', new Settings());

    $container
        ->add('logger', new LoggerDependency())
        ->addArgument($container->get('settings'))
    ;

    //TODO: this is the old CAKEPHP connection ... keep it here until it isn't used anymore.
    if (ConnectionManager::getConfig('default') == null) {
        $dbConfig = $container->get('settings')['database'];
        $dbDefault = $container->get('settings')['default_database'];
        $dsnConfig = ConnectionManager::parseDsn($dbConfig[$dbDefault]['cake_dsn']);
        $dnsConfig['username'] = $dbConfig[$dbDefault]['user'];
        $dnsConfig['password'] = $dbConfig[$dbDefault]['pass'];
        ConnectionManager::setConfig('default', $dsnConfig);
    }

    // Add all dependencies to the container
    $container
        ->add('pdo_db', new DatabaseDependency())
        ->addArgument($container->get('settings'))
    ;

    $container
        ->add('filesystem', new FileSystemDependency())
        ->addArgument($container->get('settings'))
    ;

    $container
        ->add('template', new TemplateDependency())
        ->addArgument($container->get('settings'))
    ;

    $container
        ->add('mailer', new MailerDependency())
        ->addArgument($container->get('settings'))
    ;

    $container
        ->add('converter', new ConvertDependency())
        ->addArgument($container->get('settings'))
    ;

    $application = AppFactory::create();
    $application->setBasePath($basePath);

    // Handle middleware
    $middlewareFn = require __DIR__ . '/middleware.php';
    $middlewareFn($application);

    return $application;
}
