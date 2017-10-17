<?php
namespace Backend;

use Zend\ModuleManager\Feature;
use Zend\Loader;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Zend\Crypt\Password\Bcrypt;

class Module
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php'
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Backend\Model\AuthStorage' => function ($sm) {
                    return new \Backend\Model\AuthStorage('moneycompare');
                },
                'AuthService' => function ($sm) {
                    $credentialValidationCallback = function($dbCredential, $requestCredential) {
                        return (new Bcrypt())->verify($requestCredential, $dbCredential);
                    };
                    
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new AuthAdapter($dbAdapter, 'users', 'email', 'password', $credentialValidationCallback);
                    
                    $select = $dbTableAuthAdapter->getDbSelect();
                    $select->where('status = 1');
                    
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Backend\Model\AuthStorage'));
                    return $authService;
                },
            ),
        );
    }
}
