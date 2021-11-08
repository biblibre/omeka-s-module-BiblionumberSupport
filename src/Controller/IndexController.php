<?php

namespace BiblionumberSupport\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Omeka\Mvc\Exception\NotFoundException;

class IndexController extends AbstractActionController
{
    /**
     * Redirect within item with biblionumber meta.
     */
    public function redirectAction()
    {
        $siteSlug = $this->params()->fromRoute('site-slug');
        $biblionumber = $this->params()->fromRoute('biblionumber');
        $kohaVocabPropId = $this->api()->searchOne('properties', ['term' => 'koha:biblionumber'])->getContent()->id();
    
        $item = $this->api()->searchOne('items', ['property' => [['property' => $kohaVocabPropId, 'text' => $biblionumber, 'type' => 'eq']]])->getContent();
        
        if ($item) {
            $this->redirect()->toUrl($item->siteUrl($siteSlug));
        } else {
            throw new NotFoundException;
        }
    }
}
