<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;

class Auth extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $authService;
    protected $serviceLocator;

    public function __invoke()
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $application_model_user = $sm->get('application_model_user');
        if ($this->getAuthService()->hasIdentity()) {
            $identity = $this->getAuthService()->getStorage()->read();
            $user = $application_model_user->fetchRow(array("email" => $identity));
            return $user;
        } else {
            return false;
        }
    }

    public function getAuthService()
    {
        $sm = $this->serviceLocator->getServiceLocator();
        if (!$this->authService) {
            $this->authService = $sm->get('AuthService');
        }

        return $this->authService;
    }
    
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
