<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class Menu extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $authService;
    protected $serviceLocator;
    
    public function __invoke($options)
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $translator = $sm->get('translator');
        
        $group_id  = $options['group_id'] ? $options['group_id'] : 1;
        $ul_id     = $options['ul_id'] ? $options['ul_id'] : 'primary-menu';
        $ul_class  = $options['ul_class'] ? $options['ul_class'] : 'nav navbar-nav';
        $auth_form = $options['auth'] ? true : false;
        
        $application_model_menu = $sm->get('application_model_menu');

        $menus = $application_model_menu->fetchAll(array("group_id" => $group_id));
        $html = '';
        $data = array();
        if(count($menus) > 0) {
            foreach ($menus as $menu) {
                $data[] = array(
                    'id'        => $menu->getId(),
                    'parent_id' => $menu->getParent(),
                    'name'      => $menu->getTitle(),
                    'route'     => $menu->getRoute(),
                    'action'    => $menu->getAction(),
                    'value'     => $menu->getValue(),
                );
            }
        }
        $result = $this->makeTree($data, 'id', 'parent_id', 'sub');
        if(count($result) > 0) {
            $config = $sm->get('config');
            $path = $config['application']['base_path'] ? $config['application']['base_path'] : '/';
                
            $routeMatch = $sm->get('Application')->getMvcEvent()->getRouteMatch();
            if($routeMatch) {
                $routeName   = $routeMatch->getMatchedRouteName();
                $routeAction = $routeMatch->getParam('action');
                $routeId     = $routeMatch->getParam('id');
                $routeSeo    = $routeMatch->getParam('seo');
            }
            // Level 1    
            $html .= '<ul id="'.$ul_id.'" class="'.$ul_class.'">';
            $html .= '<li class="'.($routeName === 'home' ? 'active' : '').'"><a href="/"><i class="fa fa-home"></i> '.$translator->translate("Home").'</a></li>';
            foreach($result as $id => $menu) {
                $router = $sm->get('Application')->getMvcEvent()->getRouter();
                $route  = $menu['route'];
                $action = $menu['action'];
                $value  = $menu['value']; 
                
                $class  = "";
                if($routeName === "loan_application") {
                    if($routeAction === $action) $class = ' active';
                }
                
                if($route) {
                    if($route === 'page') {
                        $url = $router->assemble(array('action' => $action, 'seo' => $value), array('name' => $route));
                    } else {
                        $url = $router->assemble(array('action' => $action), array('name' => $route));    
                    }
                } 
                else $url = "#";
                
                // Level 2
                if($menu['sub']) {
                    $sub_menu = $menu['sub'];
                    $html .= '<li aria-haspopup="true" class="menu-dropdown classic-menu-dropdown'.$class.'"><a href="javascript:;">'.$menu['name'].'<span class="arrow"></span></a>';
                    $html .= '<ul class="dropdown-menu pull-left">';
                    foreach ($sub_menu as $submenu) {
                        if($submenu['route'] === $routeName && $routeAction === $submenu['action'] && $routeSeo === $submenu['value']) $class = ' active'; // business-loan
                        else $class = "";
                        
                        if($submenu['route']) $url = $router->assemble(array('action' => $submenu['action'], 'seo' => $submenu['value']), array('name' => $submenu['route']));
                        else $url = "#";
                        
                        // Level 3
                        if($submenu['sub']) {
                            $sub_menu_v3 = $submenu['sub'];
                            $html .= '<li aria-haspopup="true" class="dropdown-submenu'.$class.'"><a href="'.$url.'">'.$submenu['name'].'<span class="arrow"></span></a>';
                            $html .= '<ul class="dropdown-menu">';
                            foreach ($sub_menu_v3 as $submenu3) {
                                if($submenu3['route'] === $routeName) $class = ' active';
                                
                                if($submenu3['route']) $url = $router->assemble(array('action' => $submenu3['action'], 'seo' => $submenu3['value']), array('name' => $submenu3['route']));
                                else $url = "#";
                                $html .= '<li aria-haspopup="true" class="'.$class.'"><a href="'.$url.'" class="nav-link">'.$submenu3['name'].'</a></li>'; 
                            }
                            $html .= '</ul>';
                            $html .= '</li>';
                        } else {
                            $style="";
                            $new="";
                            if($submenu['name']=="Business Loan Calculator")
                            {
                                $new="<span style='color:green;font-weight:bold'>(NEW!!)</span>";
                                $style="style='background-color:coral'";
                                $html .= '<li id="new" aria-haspopup="true" class="'.$class.'"><a href="'.$url.'" class="nav-link" '.$style.'>'.$submenu['name'].$new.'</a></li>';
                            }
                            $html .= '<li aria-haspopup="true" class="'.$class.'"><a href="'.$url.'" class="nav-link" >'.$submenu['name'].'</a></li>';    
                        }
                    }
                    $html .= '</ul>';
                    $html .= '</li>';
                } else {
                    if($routeName === 'page' && $routeSeo === $value) $class = ' active';
                    $html .= '<li class="'.$class.'"><a href="'.$url.'" data-param="'.$routeSeo.'">'.$menu['name'].'</a></li>';
                }
            }
            if($auth_form) {
                if($this->getAuthService()->hasIdentity()) {
                    $identity = $this->getAuthService()->getIdentity();
                    $application_model_user = $sm->get("application_model_user");
                    $user = $application_model_user->fetchRow(array("email" => $identity));
                    if($user) {
                        $avatar = $user->getAvatar();
                        $dir_avatar = "data/user/".$user->getId()."/s_".$avatar;
                        if(file_exists($dir_avatar) === false) {
                            $dir_avatar = "/data/user/no-avatar-32.png";
                        }
                        $basePath = $sm->get('viewhelpermanager')->get('basePath');
                        $router = $sm->get('Application')->getMvcEvent()->getRouter();
                        $html .= '<li class="dropdown dropdown-user dropdown-dark">';
                        $html .= '<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"><img alt="'.$user->getDisplayName().'" class="img-circle" src="'.$basePath($dir_avatar).'"><span class="username username-hide-mobile">'.$user->getDisplayName().'</span></a>';
                        $html .= '<ul class="dropdown-menu dropdown-menu-default">';
                        $html .= '<li><a href="'.$router->assemble(array("action" => "profile"), array('name' => 'frontend_user')).'"><i class="icon-user"></i> '.$translator->translate("My Profile").' </a></li>';
                        $html .= '<li><a href="'.$router->assemble(array("action" => "applicable"), array('name' => 'frontend_user')).'"><i class="icon-layers"></i> '.$translator->translate("My Application").' </a></li>';
                        $html .= '<li><a href="'.$router->assemble(array("action" => "logout"), array('name' => 'frontend_user')).'"><i class="icon-key"></i> '.$translator->translate("Log Out").' </a></li>';
                        $html .= '</ul>';
                        $html .= '</li>';
                    }
                } else {
                    switch ($routeName) {
                        case 'frontend_user':
                            if($routeAction === 'signup' || $routeAction === 'signin') {
                                $html .= '<li class="sign-up"><a href="#auth" class="auth-button">'.$translator->translate("Sign Up").' <i class="fa fa-long-arrow-right"></i></a></li>'; 
                            } else if($routeAction === 'auth') {
                                $html .= '<li class="sign-up"><a href="'.$router->assemble(array("action" => "auth"), array('name' => 'frontend_user')).'">'.$translator->translate("Sign Up").' <i class="fa fa-long-arrow-right"></i></a></li>'; 
                            } else {
                                $html .= '<li class="sign-up"><a href="#auth" class="fancybox-button">'.$translator->translate("Sign Up").' <i class="fa fa-long-arrow-right"></i></a></li>'; 
                            }
                        break;
                        /*
                        case 'personal_loan':
                            $html .= '<li class="sign-up"><a href="'.$router->assemble(array("action" => "signin"), array('name' => 'frontend_user')).'">'.$translator->translate("Sign Up").' <i class="fa fa-long-arrow-right"></i></a></li>'; 
                        break;
                        */
                        
                        default:
                            $html .= '<li class="sign-up"><a href="#auth" class="fancybox-button">'.$translator->translate("Sign Up").' <i class="fa fa-long-arrow-right"></i></a></li>'; 
                        break;
                    }
                       
                }    
            }
            //$html .= '<li class="menu-tools"><a href="#"><i class="fa fa-calculator"></i> '.$translator->translate("Tools").'</a></li>';
            $html .= '</ul>';
        }
        return $html;
    }
    
    public function makeTree($data = [], $key = 'id', $parent_key = 'parent_id', $node_name = 'childs', $parent_code = 0) {
        $parent_ids = array_column($data, $parent_key);
        $result = [];
        foreach ($data as $k=>$v) {
            if($v[$parent_key] == $parent_code){
                $result[$v[$key]] = $v;
                if(in_array($v[$key], $parent_ids)){
                    $result[$v[$key]][$node_name] = $this->makeTree($data, $key , $parent_key, $node_name, $v[$key]);
                } 
            }
        }
        return $result;
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
