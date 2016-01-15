<?php

namespace Soyuka\SeedBundle\Command;

use Soyuka\SeedBundle\Core\Seeds;

final class UnloadSeedsCommand extends Seeds
{
    protected function configure()
    {
        $this->method = 'unload';
        parent::configure();
    }
}
