<?php

namespace Soyuka\SeedBundle\Extensions;

use Symfony\Component\Console\Input\InputInterface;
use Soyuka\SeedBundle\Model\SeedExtension;

class Order implements SeedExtension
{
    /**
     * {@inheritdoc}
     */
    public function apply(array &$commands, InputInterface $input)
    {
        //Sort through getOrder
        usort($commands, function ($a, $b) {
            return $a->getOrder() - $b->getOrder();
        });
    }
}
