<?php

namespace Soyuka\SeedBundle\Command;

use Soyuka\SeedBundle\Core\Seeds;

final class LoadSeedsCommand extends Seeds
{
    protected function configure()
    {
        $this->method = 'load';
        parent::configure();
    }
}
