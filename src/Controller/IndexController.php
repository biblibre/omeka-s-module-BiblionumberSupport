<?php

namespace BiblionumberSupport\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Doctrine\DBAL\Connection;
use Omeka\Api\Adapter\Manager as ApiAdapterManager;
use Omeka\Mvc\Exception\NotFoundException;

class IndexController extends AbstractActionController
{
    protected $connection;
    protected $apiAdapterManager;

    /**
     * Redirect within item with biblionumber meta.
     */
    public function redirectAction()
    {
        try {
            $siteSlug = $this->params()->fromRoute('site-slug');
            $biblionumber = $this->params()->fromRoute('biblionumber');

            $kohaBiblionumberProperty = $this->api()->searchOne('properties', ['term' => 'koha:biblionumber'])->getContent();
            if (!$kohaBiblionumberProperty) {
                throw new NotFoundException('Property "koha:biblionumber" not found.');
            }

            $connection = $this->getConnection();
            $sql = <<<'SQL'
                SELECT `i`.`id`
                FROM `item` AS `i`
                JOIN `value` AS `v` ON `i`.`id` = `v`.`resource_id`
                WHERE `v`.`type` = 'literal'
                    AND `v`.`property_id` = :property
                    AND `v`.`value` = :val;
            SQL;

            $itemId = $connection->executeQuery($sql, [
                'property' => $kohaBiblionumberProperty->id(),
                'val' => $biblionumber,
            ])->fetchOne();

            if (!$itemId) {
                throw new NotFoundException('Item not found for the given biblionumber.');
            }

            $itemAdapter = $this->getApiAdapterManager()->get('items');
            $qb = $itemAdapter->getEntityManager()->createQueryBuilder();
            $qb->select('omeka_root')
                ->from('Omeka\Entity\Item', 'omeka_root')
                ->where('omeka_root.id = :id')
                ->setParameter('id', $itemId);

            $itemRepresentation = $itemAdapter->getRepresentation($qb->getQuery()->getSingleResult());

            return $this->redirect()->toUrl($itemRepresentation->siteUrl($siteSlug));
        } catch (\Exception $e) {
            $view = new ViewModel();
            $view->setVariable('biblionumber', $biblionumber);

            return $view;
        }
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
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
