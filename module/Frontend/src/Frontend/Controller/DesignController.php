<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;

class DesignController extends AbstractActionController
{
public function indexAction()
    {
        $this->layout('layout/design');
    }
}