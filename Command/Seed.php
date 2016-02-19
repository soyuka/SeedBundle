<?php

namespace Soyuka\SeedBundle\Command;

use Soyuka\SeedBundle\Core\Seed as SeedCompatibility;
use Soyuka\SeedBundle\Model\SeedOrderInterface;
use Soyuka\SeedBundle\Model\SeedInterface;

/**
 * @codeCoverageIgnore
 */
abstract class Seed extends SeedCompatibility implements SeedOrderInterface, SeedInterface
{
    public function getOrder(): int
    {
        return 0;
    }
}
