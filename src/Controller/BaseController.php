<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected array $variables = [];

    protected function assign(string $key, mixed $val): void
    {
        $this->variables[$key] = $val;
    }
}
