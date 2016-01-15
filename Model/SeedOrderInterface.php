<?php

namespace Soyuka\SeedBundle\Model;

/**
 * Interface SeedInterface.
 */
interface SeedOrderInterface
{
    /**
     * get the seed order (default is 0).
     *
     * @return int
     */
    public function getOrder();
}
