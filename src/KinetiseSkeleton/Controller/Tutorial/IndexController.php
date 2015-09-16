<?php

namespace KinetiseSkeleton\Controller\Tutorial;

use KinetiseSkeleton\Controller\AbstractController;
use Silex\Application;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $app = $this->getApplication();

        return $this->getTwig()->render('tutorial/index/index.html.twig');
    }
}