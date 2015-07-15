<?php

namespace Soyuka\SeedBundle\Command;

class UnloadSeedsCommand extends Seeds
{
    protected function configure() {
        $this->method = 'unload';
        parent::configure();
    }
}
