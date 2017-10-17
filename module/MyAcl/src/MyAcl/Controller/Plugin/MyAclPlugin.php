<?php
namespace MyAcl\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Session\Container as SessionContainer,
    Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;

use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use Zend\Log\Formatter\Simple;
    
class MyAclPlugin extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    protected $sesscontainer ;

    private function getSessContainer()
    {
        if (!$this->sesscontainer) {
            $this->sesscontainer = new SessionContainer('progress');
        }
        return $this->sesscontainer;
    }
    
    public function doAuthorization($e) {
        $sm = $this->serviceLocator->getServiceLocator();
        $acl = new Acl();
        //$acl->deny(); // on by default
        $acl->allow(); // this will allow every route by default so then you have to explicitly deny all routes that you want to protect.
        
        # RESOURCES ########################################
        $config = $sm->get('Config');
        $invokables = $config['controllers']['invokables'];
        $privileges = array();
        foreach ($invokables as $key => $value) {
            $value = str_replace("\\", "/", $value);
            $controllerName = explode('/', $value)[0];
            if($controllerName === 'Backend') {
                $privileges[] = $key;
            }
            //$route
            if(in_array($key, $privileges)) {
                $key = "admin/".$key;
                $key = str_replace("admin/admin", "admin", $key);
            } 
            $acl->addResource($key);
        }
		# end RESOURCES ########################################
        
        # ROLES ############################################
        $groupMapper = $sm->get('application_model_role');
        $groups = $groupMapper->fetchAll(array('status' => 1));
        foreach ($groups as $group) {
            $acl->addRole(new Role($group->getKey()));
            
            ################ PERMISSIONS #######################
            $allow_arr = \Zend\Json\Json::decode ( $group->getAllow() );
			$allow_arr = array_filter($allow_arr);
			if(count($allow_arr) > 0) {
				foreach ($allow_arr as $k => $v) {
					$router = explode("--", $v);
					$resource = $router[0];
                    $action = $router[1];
					$acl->allow($group->getKey(), $resource, $action);                   
				}
			}
            $deny_arr = \Zend\Json\Json::decode ( $group->getDeny() );
			$deny_arr = array_filter($deny_arr);
			if(count($deny_arr) > 0) {
				foreach ($deny_arr as $k => $v) {
					$router = explode("--", $v);
                    $resource = $router[0];
                    $action = $router[1];
					$acl->deny($group->getKey(), $resource, $action);
				}
			}          
            ################ end PERMISSIONS #####################
        }
        # end ROLES ########################################
        
        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        $moduleName = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
        
        $role_name = 'guest';
        $auth = $sm->get('AuthService');
        if ($auth->hasIdentity()) {
            $userMapper = $sm->get('application_model_user');
            $identity = $auth->getIdentity();
            $user = $userMapper->fetchRow(array("email" => $identity));
            
            $role_id = $user->getRoleId();
            
            $group = $groupMapper->fetchRow(array('id' => $role_id));
            $role_name = $group->getKey();
        }
        //$role = (!$role_name) ? 'guest' : $role_name;
        //$role = (! $this->getSessContainer()->role ) ? 'guest' : $this->getSessContainer()->role;
        
        $routeMatch = $e->getRouteMatch();
		$actionName = strtolower($routeMatch->getParam('action', 'not-found'));	// get the action name
        $controllerName = $routeMatch->getParam('controller', 'not-found');	// get the controller name
        
        if($moduleName === 'backend') {
            $controllerName = "admin/".$controllerName;
            $controllerName = str_replace("admin/admin", "admin", $controllerName);
        }
        
        $remote = new \Zend\Http\PhpEnvironment\RemoteAddress;
        $ip_address = $remote->getIpAddress();
        
        $log  = $ip_address.' - Role: '.$role_name; 
		$log .= ' - Module: '.$moduleName; 
		$log .= ' - ControllerClass: '.$controllerClass; 
		$log .= ' - ControllerName: '.$controllerName; 
		$log .= ' - Action: '.$actionName;
        $this->log($log);
        
        #################### Check Access ########################
        if (!$acl->isAllowed($role_name, $controllerName, $actionName)){
            $router   = $e->getRouter();
            $url      = $router->assemble(array('action' => 'error'), array('name' => 'page'));
            $response = $e->getResponse();
            $response->setStatusCode(302);
            
            // redirect to login page or other page.
            $response->getHeaders()->addHeaderLine('Location', $url);
            $e->stopPropagation();            
        }	      
    }
    
    public function log($log) {
        // Sql log
        $writer = new Stream(getcwd() . "/data/log/access.log");
		$format = $log . PHP_EOL;
		$formatter = new Simple($format);
		$writer->setFormatter($formatter);
        
        $logger = new Logger();
		$logger->addWriter($writer);
		$logger->info('sql runs!!!');
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}