<?php

namespace BiblionumberSupport\Controller;

use Doctrine\ORM\EntityManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Omeka\Api\Adapter\Manager as ApiAdapterManager;
use Omeka\Mvc\Exception\NotFoundException;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $entityManager;
    protected $apiAdapterManager;

    /**
     * Redirect within item with biblionumber meta.
     */
    public function redirectAction()
    {
        try {
            $em = $this->getEntityManager();

            $siteSlug = $this->params()->fromRoute('site-slug');
            $biblionumber = $this->params()->fromRoute('biblionumber');

            $kohaBiblionumberProperty = $this->api()->searchOne('properties', ['term' => 'koha:biblionumber'])->getContent();
            if (!$kohaBiblionumberProperty) {
                throw new NotFoundException;
            }

            $query = $em->createQuery('SELECT i FROM Omeka\Entity\Item i JOIN i.values v WHERE v.type = :type AND v.property = :property AND v.value = :value');
            $query->setParameter('type', 'literal');
            $query->setParameter('property', $kohaBiblionumberProperty->id());
            $query->setParameter('value', $biblionumber);
            $query->setMaxResults(1);
            $item = $query->getSingleResult();
            if (!$item) {
                throw new NotFoundException;
            }

            $itemAdapter = $this->getApiAdapterManager()->get('items');
            $itemRepresentation = $itemAdapter->getRepresentation($item);

            return $this->redirect()->toUrl($itemRepresentation->siteUrl($siteSlug));
        } catch (\Exception $e) {
            $view = new ViewModel;
            $view->setVariable('biblionumber', $biblionumber);

            return $view;
        }
    }

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setApiAdapterManager(ApiAdapterManager $apiAdapterManager)
    {
        $this->apiAdapterManager = $apiAdapterManager;
    }

    public function getApiAdapterManager()
    {
        return $this->apiAdapterManager;
    }
}
