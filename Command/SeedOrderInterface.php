<?php

namespace Soyuka\SeedBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface SeedInterface
 */
interface SeedOrderInterface
{
    /**
     * get the seed order (default is 0)
     * @return int
     */
    public function getOrder();
}
