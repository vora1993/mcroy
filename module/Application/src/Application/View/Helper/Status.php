<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class Status extends AbstractHelper implements ServiceLocatorAwareInterface
{
    public function __invoke($object)
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $translator = $sm->get('translator');
        switch ($object->getStatus()){
            case 1;
                $status = '<span class="label label-sm label-success"> '.$translator->translate("Approved").' </span>';
            break;
                                        
            case 2;
                $status = '<span class="label label-sm label-danger"> '.$translator->translate("Cancelled").' </span>';
            break;
                                        
            case 3;
                $status = '<span class="label label-sm label-danger"> '.$translator->translate("Rejected").' </span>';
            break;
            
            case 4;
                $status = '<span class="label label-sm label-success"> '.$translator->translate("Paid").' </span>';
            break;
            
            default:
                $status = '<span class="label label-sm label-warning"> '.$translator->translate("Pending").' </span>';
            break;
        }
        return $status;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
