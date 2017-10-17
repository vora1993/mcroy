<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class Setting extends AbstractHelper implements ServiceLocatorAwareInterface
{
    public function __invoke()
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $application_model_setting = $sm->get('application_model_setting');
        
        $settings = $application_model_setting->fetchAll();
        $obj = new \stdClass;
        foreach ($settings as $setting) {
            $key = $setting->getKey();
            $value = $setting->getValue();
            $obj->{$key} = $value;    
        }
        return $obj;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
