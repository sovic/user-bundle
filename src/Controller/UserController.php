<?php

namespace UserBundle\Controller;

use UserBundle\Controller\Trait\SettingsControllerTrait;
use UserBundle\Controller\Trait\UserControllerTrait;

class UserController extends BaseController
{
    use SettingsControllerTrait;
    use UserControllerTrait;
}
