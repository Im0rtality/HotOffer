<?php
/**
 * User: lverzukauskas
 * Date: 13.7.16
 * Time: 14.00
 */

class HotOfferOxArticle extends HotOfferOxArticle_parent{

    /**
     * Returns nfq_hotoffer value
     *
     * @return bool
     */
    public function isHotOffer()
    {
        return $this->oxarticles__nfq_hotoffer->value == "1";
    }
}