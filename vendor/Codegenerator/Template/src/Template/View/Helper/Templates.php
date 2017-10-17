<?php

namespace {$module}\View\Helper;

use Zend\View\Helper\AbstractHelper;  
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class {$module} extends AbstractHelper implements ServiceLocatorAwareInterface
{
    private $serviceLocator;
    
    public function __invoke()
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $mapperTable = $sm->get('{$moduleLower}_mapper');
        
        $rows = $mapperTable->fetchAll(array('status' => 1));
        return $rows;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
