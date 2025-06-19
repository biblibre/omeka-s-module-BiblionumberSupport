<?php

/*
 * Copyright BibLibre, 2016-2021
 *
 * This software is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */

namespace BiblionumberSupport;

use Omeka\Module\AbstractModule;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);
       
        $acl = $this->getServiceLocator()->get('Omeka\Acl');
        $acl->allow(null, 'BiblionumberSupport\Controller\Index');

    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Api\Adapter\ItemAdapter',
            'api.search.query',
            [$this, 'onItemApiSearchQuery']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Item',
            'view.advanced_search',
            [$this, 'onItemViewAdvancedSearch']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Item',
            'view.search.filters',
            [$this, 'onItemViewSearchFilters']
        );

        $sharedEventManager->attach(
            'Omeka\Api\Adapter\MediaAdapter',
            'api.search.query',
            [$this, 'onMediaApiSearchQuery']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.advanced_search',
            [$this, 'onMediaViewAdvancedSearch']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Media',
            'view.search.filters',
            [$this, 'onMediaViewSearchFilters']
        );
    }

    public function onItemApiSearchQuery(Event $event)
    {
        $adapter = $event->getTarget();
        $qb = $event->getParam('queryBuilder');
        $request = $event->getParam('request');

        $services = $this->getServiceLocator();
        $api = $services->get('Omeka\ApiManager');

        [$kohaBiblionumberProperty] = $api->search('properties', ['term' => 'koha:biblionumber'])->getContent();
        if (!$kohaBiblionumberProperty) {
            //$qb->andWhere('0');
            return;
        }

        $ids = $request->getValue('biblionumber', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $ids = array_filter($ids);
        if ($ids) {
            $valuesAlias = $adapter->createAlias();
            $qb->leftJoin(
                'omeka_root.values',
                $valuesAlias,
                'WITH',
                $qb->expr()->eq("$valuesAlias.property", (int) $kohaBiblionumberProperty->id())
            );
            $qb->andWhere(
                $qb->expr()->in("$valuesAlias.value", $adapter->createNamedParameter($qb, $ids))
            );
        }
    }

    public function onItemViewAdvancedSearch(Event $event)
    {
        $partials = $event->getParam('partials');

        $partials[] = 'biblionumber-support/common/advanced-search/biblionumber';

        $event->setParam('partials', $partials);
    }

    public function onItemViewSearchFilters(Event $event)
    {
        $view = $event->getTarget();
        $query = $event->getParam('query');
        $filters = $event->getParam('filters');

        $ids = $query['biblionumber'] ?? [];
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $ids = array_filter($ids);
        if ($ids) {
            $filters[$view->translate('Biblionumber')] = $ids;
        }

        $event->setParam('filters', $filters);
    }

    public function onMediaApiSearchQuery(Event $event)
    {
        $adapter = $event->getTarget();
        $qb = $event->getParam('queryBuilder');
        $request = $event->getParam('request');

        $services = $this->getServiceLocator();
        $api = $services->get('Omeka\ApiManager');

        [$kohaBiblionumberProperty] = $api->search('properties', ['term' => 'koha:biblionumber'])->getContent();
        if (!$kohaBiblionumberProperty) {
            $qb->andWhere('0');
            return;
        }

        $ids = $request->getValue('biblionumber', []);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $ids = array_filter($ids);
        if ($ids) {
            $itemAlias = $adapter->createAlias();
            $qb->leftJoin('omeka_root.item', $itemAlias);

            $valuesAlias = $adapter->createAlias();
            $qb->leftJoin(
                "$itemAlias.values",
                $valuesAlias,
                'WITH',
                $qb->expr()->eq("$valuesAlias.property", (int) $kohaBiblionumberProperty->id())
            );
            $qb->andWhere(
                $qb->expr()->in("$valuesAlias.value", $adapter->createNamedParameter($qb, $ids))
            );
        }
    }

    public function onMediaViewAdvancedSearch(Event $event)
    {
        $partials = $event->getParam('partials');

        $partials[] = 'biblionumber-support/common/advanced-search/biblionumber';

        $event->setParam('partials', $partials);
    }

    public function onMediaViewSearchFilters(Event $event)
    {
        $view = $event->getTarget();
        $query = $event->getParam('query');
        $filters = $event->getParam('filters');

        $ids = $query['biblionumber'] ?? [];
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $ids = array_filter($ids);
        if ($ids) {
            $filters[$view->translate('Biblionumber')] = $ids;
        }

        $event->setParam('filters', $filters);
    }
}
