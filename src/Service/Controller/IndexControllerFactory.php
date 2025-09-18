<?php

namespace BiblionumberSupport\Service\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use BiblionumberSupport\Controller\IndexController;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $controller = new IndexController();
        $controller->setConnection($services->get('Omeka\Connection'));
        $controller->setApiAdapterManager($services->get('Omeka\ApiAdapterManager'));

        return $controller;
    }
}
