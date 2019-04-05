<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;

class DesignController extends AbstractActionController
{
public function indexAction()
    {
    	$application_model_design = $this->getServiceLocator()->get('application_model_design');
        $loan = $application_model_design->fetchRow(array('id' => 1));
        $this->layout('layout/design');
        return array('loan' => $loan);
    }
}