<?php

namespace Soyuka\SeedBundle\Command;

class LoadSeedsCommand extends Seeds
{
    protected function configure() {
        $this->method = 'load';
        parent::configure();
    }
}
