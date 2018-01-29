<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class BankInterestRate extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function __invoke($condition=null)
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $application_model_bank_interest_rate = $sm->get('application_model_bank_interest_rate');
        $rows = $application_model_bank_interest_rate->fetchAll($condition);
        return $rows;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
