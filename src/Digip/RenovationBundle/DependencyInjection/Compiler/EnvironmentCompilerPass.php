<?php

namespace Digip\RenovationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EnvironmentCompilerPass implements CompilerPassInterface
{
    /**
     * Search for the 'sites/default/settings.php' file and override
     * parameters.yml with the database config from this file.
     *
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        // do we have an environment settings file?
        $root = dirname($container->getParameter('kernel.root_dir'));
        $settingsFile = $root . '/sites/default/settings.php';

        if (file_exists($settingsFile)) {

            /*
             * WARNING: this file contains ini_set() calls
             */
            include $settingsFile;

            /*
             * Change DB params
             */
            if (isset($databases) && isset($databases['default']) && isset($databases['default']['default'])) {
                $db = $databases['default']['default'];
                $this->setDatabaseParams($container, $db);
            } else {
                throw new \Exception('Environment settings.php file was found, but database configuration could not be read');
            }

        }
    }

    protected function setDatabaseParams(ContainerBuilder $container, array $dbParams)
    {
        // set the parameters
        $container->setParameter('database_host',       $dbParams['host']);
        $container->setParameter('database_port',       $dbParams['port']);
        $container->setParameter('database_name',       $dbParams['database']);
        $container->setParameter('database_user',       $dbParams['username']);
        $container->setParameter('database_password',   $dbParams['password']);

        /*
         * update definitions
         */

        // doctrine
        if ($container->hasDefinition('doctrine.dbal.default_connection')) {
            $doctrineConfig = $container->getDefinition('doctrine.dbal.default_connection');

            $arguments = $doctrineConfig->getArguments();
            $params = $arguments[0];
            $params['host']     = $container->getParameter('database_host');
            $params['port']     = $container->getParameter('database_port');
            $params['dbname']   = $container->getParameter('database_name');
            $params['user']     = $container->getParameter('database_user');
            $params['password'] = $container->getParameter('database_password');
            $arguments[0] = $params;

            $doctrineConfig->setArguments($arguments);
        }
    }
} 