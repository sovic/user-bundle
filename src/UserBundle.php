<?php

namespace UserBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
    public function registerCommands(Application $application): void
    {
        // noop
    }
}
