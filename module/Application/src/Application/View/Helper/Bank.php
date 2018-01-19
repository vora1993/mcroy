<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class Bank extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function __invoke($condition=null,$getall=0)
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $application_model_bank = $sm->get('application_model_bank');
        if($getall==0)
        {
            $row = $application_model_bank->fetchRow($condition);
        }else{
            $row = $application_model_bank->fetchAll($condition);
        }
        
        return $row;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
