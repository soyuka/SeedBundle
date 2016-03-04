<?php

namespace Soyuka\SeedBundle\Extensions;

use Symfony\Component\Console\Input\InputInterface;
use Soyuka\SeedBundle\Model\SeedExtensionInterface;
use Soyuka\SeedBundle\Core\Seed;

class Order implements SeedExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(array &$commands, InputInterface $input)
    {
        //Sort through getOrder
        usort($commands, function (Seed $a, Seed $b) {
            return $a->getOrder() - $b->getOrder();
        });
    }
}
