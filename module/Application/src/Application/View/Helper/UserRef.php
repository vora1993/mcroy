<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class UserRef extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function __invoke($condition=null)
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $application_model_user = $sm->get('application_model_user_ref');
        $row = $application_model_user->fetchRow($condition);
        return $row;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
