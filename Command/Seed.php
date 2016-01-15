<?php

namespace Soyuka\SeedBundle\Command;

use Soyuka\SeedBundle\Core\Seed as SeedCompatibility;
use Soyuka\SeedBundle\Model\SeedOrderInterface;

/**
 * @codeCoverageIgnore
 */
abstract class Seed extends SeedCompatibility implements SeedOrderInterface
{
    public function getOrder()
    {
        return 0;
    }
}
