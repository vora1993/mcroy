<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

class MortgageCalculator extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function __invoke()
    {
        /*$viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('mortgage_calculator');
        $viewModel->setVariables(array("loan" => 1, "compare" => 2));
        return $viewModel;*/
        $template = "mortgage_calculator";
        return $template;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
