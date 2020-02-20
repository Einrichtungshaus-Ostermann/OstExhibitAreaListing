<?php

/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - Exhibit Area Listing
 *
 * @package   OstExhibitAreaListing
 *
 * @author    Tim Windelschmidt <tim.windelschmidt@fionera.de>
 * @copyright 2020 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

class Shopware_Controllers_Frontend_OstExhibitAreaListing extends Enlight_Controller_Action
{
    /**
     * ...
     *
     * @throws Exception
     */
    public function preDispatch()
    {
        $viewDir = $this->container->getParameter('ost_exhibit_area_listing.view_dir');

        $this->get('template')->addTemplateDir($viewDir);

        parent::preDispatch();
    }


    public function indexAction()
    {
        $koje = $this->Request()->getParam('koje');

        $qb = Shopware()->Models()->getDBALQueryBuilder();

        $articles = $qb
            ->from('s_articles_details', 'details')
            ->innerJoin('details', 's_articles_attributes', 'attr', 'details.id = attr.articledetailsID')
            ->select('*')
            ->where($qb->expr()->orX(
                $qb->expr()->like('attr.attr21', $qb->createNamedParameter($koje)),
                $qb->expr()->like('attr.attr21',  $qb->createNamedParameter('%,' .$koje)),
                $qb->expr()->like('attr.attr21', $qb->createNamedParameter($koje . ',%')),
                $qb->expr()->like('attr.attr21', $qb->createNamedParameter('%,' . $koje . ',%'))
            ))
            ->groupBy('details.ordernumber')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC)
        ;

        $legacyStructConverter = Shopware()->Container()->get('legacy_struct_converter');
        $listProductService = Shopware()->Container()->get('shopware_storefront.list_product_service');
        $productContext = Shopware()->Container()->get('shopware_storefront.context_service')->getProductContext();

        if ($productContext === null) {
            throw new Enlight_Controller_Exception('ProductContext is null');
        }

        $products = [];
        foreach ($articles as $article) {
            $product = $listProductService->get($article['ordernumber'], $productContext);

            $products[$article['ordernumber']] = $legacyStructConverter->convertListProductStruct($product);
        }


        $this->View()->assign('sCategoryContent', [
            'cmsheadline' => 'Artikel der Koje: ' . $koje
        ]);
        $this->View()->assign('sArticles', $products);
        $this->View()->assign('showListing', true);
    }
}
