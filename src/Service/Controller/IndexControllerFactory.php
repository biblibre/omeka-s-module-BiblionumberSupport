<?php

namespace BiblionumberSupport\Service\Controller;

use BiblionumberSupport\Controller\IndexController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new IndexController();
        $controller->setEntityManager($container->get('Omeka\EntityManager'));
        $controller->setApiAdapterManager($container->get('Omeka\ApiAdapterManager'));

        return $controller;
    }
}
