<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container as Session;

class Module
{
    protected $whitelist = array(
        'admin/index',
        'admin/user/login',
        'home/index',
    );

    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $em  = $app->getEventManager();
        $sm  = $app->getServiceManager();

        $session = new Session('customer');
        if($session->offsetExists('customer_id') === FALSE) {
            $session->customer_id = 0;
        }
        if($session->offsetExists('api') === FALSE) {
            $session->api = $this->generateRandomString(20);
        }
        /*
        $list = $this->whitelist;
        $auth = $sm->get('AuthService');
        $em->attach(MvcEvent::EVENT_ROUTE, function($e) use ($list, $auth) {
            $match = $e->getRouteMatch();

            // No route match, this is a 404
            if (!$match instanceof RouteMatch) {
                return;
            }

            // Route is whitelisted
            $route = $match->getMatchedRouteName();
            $action = strtolower($match->getParam('action', ''));	// get the action name
            $name = $route.'/'.$action;
            if (in_array($name, $list)) {
                return;
            }

            // User is authenticated
            if ($auth->hasIdentity()) {
                return;
            }

            // Redirect to the user login page
            $router   = $e->getRouter();
            $url      = $router->assemble(array('action' => 'error', 'seo' => '404'), array(
                'name' => 'page'
            ));

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;

        }, -100);
        */


        // Layout
        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'selectLayoutBasedOnRoute'));

        // Module layout
        $eventManager        = $e->getApplication()->getEventManager();
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config          = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Language
        $app->getEventManager()->attach(
            'dispatch',
            function($e) {
              $routeMatch = $e->getRouteMatch();
              $this->serviceManager = $e->getApplication()->getServiceManager();
              $translator = $this->serviceManager->get('translator');

              if ($routeMatch->getParam('locale') != '') {
                $translator->setLocale($routeMatch->getParam('locale'));
              } else {
                $translator->setLocale('en_US');
              }
            }, 100
        );
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function selectLayoutBasedOnRoute(MvcEvent $e)
    {
        $app    = $e->getParam('application');
        $sm     = $app->getServiceManager();
        $config = $sm->get('config');

        if (false === $config['admin']['use_admin_layout']) {
            return;
        }

        $match      = $e->getRouteMatch();
        $target = $e->getTarget();
        if (!$match instanceof RouteMatch
            || 0 !== strpos($match->getMatchedRouteName(), 'admin')
            || $target->getEvent()->getResult()->terminate()
        ) {
            return;
        }

        $layout       = $config['admin']['admin_layout_template'];
        $layout_login = $config['admin']['admin_login_template'];

        $controller = $match->getParam('controller');
        $action     = $match->getParam('action');
        $module     = $match->getParam('__NAMESPACE__');
        $route      = $match->getMatchedRouteName();
        if($route === 'admin/user' && $action === 'login') {
            $target->layout($layout_login);
        } else {
            $target->layout($layout);
        }
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'setting' => function($sm) {
                    $viewHelper = new View\Helper\Setting;
                    return $viewHelper;
                },
                'auth' => function($sm) {
                    $viewHelper = new View\Helper\Auth;
                    return $viewHelper;
                },
                'user' => function($sm) {
                    $viewHelper = new View\Helper\User;
                    return $viewHelper;
                },
                'user_ref' => function($sm) {
                    $viewHelper = new View\Helper\UserRef;
                    return $viewHelper;
                },
                'referral' => function($sm) {
                    $viewHelper = new View\Helper\Referral;
                    return $viewHelper;
                },
                'referrals' => function($sm) {
                    $viewHelper = new View\Helper\Referrals;
                    return $viewHelper;
                },
                'resize_image' => function($sm) {
                    $viewHelper = new View\Helper\ResizeImage;
                    return $viewHelper;
                },
                'folder' => function($sm) {
                    $viewHelper = new View\Helper\Folder;
                    return $viewHelper;
                },
                'mortgage_calculator' => function($sm) {
                    $viewHelper = new View\Helper\MortgageCalculator;
                    return $viewHelper;
                },
                'send_email' => function($sm) {
                    $viewHelper = new View\Helper\SendEmail;
                    return $viewHelper;
                },
                'send_email_to_user' => function($sm) {
                    $viewHelper = new View\Helper\SendEmailToUser;
                    return $viewHelper;
                },
                'status' => function($sm) {
                    $viewHelper = new View\Helper\Status;
                    return $viewHelper;
                },
                'role' => function($sm) {
                    $viewHelper = new View\Helper\Role;
                    return $viewHelper;
                },
                'roles' => function($sm) {
                    $viewHelper = new View\Helper\Roles;
                    return $viewHelper;
                },
                'banks' => function($sm) {
                    $viewHelper = new View\Helper\Banks;
                    return $viewHelper;
                },
                'bank' => function($sm) {
                    $viewHelper = new View\Helper\Bank;
                    return $viewHelper;
                },
                'BankInterestRate' => function($sm) {
                    $viewHelper = new View\Helper\BankInterestRate;
                    return $viewHelper;
                },
                'business_loan' => function($sm) {
                    $viewHelper = new View\Helper\BusinessLoan;
                    return $viewHelper;
                },
                'business_loan_package' => function($sm) {
                    $viewHelper = new View\Helper\BusinessLoanPackage;
                    return $viewHelper;
                },
                'loans' => function($sm) {
                    $viewHelper = new View\Helper\Loans;
                    return $viewHelper;
                },
                'loan' => function($sm) {
                    $viewHelper = new View\Helper\Loan;
                    return $viewHelper;
                },
                'menu' => function($sm) {
                    $viewHelper = new View\Helper\Menu;
                    return $viewHelper;
                },
                'menu_groups' => function($sm) {
                    $viewHelper = new View\Helper\MenuGroups;
                    return $viewHelper;
                },
                'categories' => function($sm) {
                    $viewHelper = new View\Helper\Categories;
                    return $viewHelper;
                },
                'category' => function($sm) {
                    $viewHelper = new View\Helper\Category;
                    return $viewHelper;
                },
                'bank_accounts' => function($sm) {
                    $viewHelper = new View\Helper\BankAccounts;
                    return $viewHelper;
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'application_model_user' => function ($sm) {
                	$model = new Model\User();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\User());
                	$model->setHydrator(new Mapper\UserHydrator());
                	return $model;
                },
                'application_model_user_ref' => function ($sm) {
                	$model = new Model\UserRef();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\UserRef());
                	$model->setHydrator(new Mapper\UserRefHydrator());
                	return $model;
                },
                'application_model_user_facebook' => function ($sm) {
                	$model = new Model\UserFacebook();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\UserFacebook());
                	$model->setHydrator(new Mapper\UserFacebookHydrator());
                	return $model;
                },
                'application_model_user_credit' => function ($sm) {
                	$model = new Model\UserCredit();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\UserCredit());
                	$model->setHydrator(new Mapper\UserCreditHydrator());
                	return $model;
                },
                'application_model_user_bank_account' => function ($sm) {
                	$model = new Model\UserBankAccount();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\UserBankAccount());
                	$model->setHydrator(new Mapper\UserBankAccountHydrator());
                	return $model;
                },
                'application_model_setting' => function ($sm) {
                	$model = new Model\Setting();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Setting());
                	$model->setHydrator(new Mapper\SettingHydrator());
                	return $model;
                },
                'application_model_role' => function ($sm) {
                	$model = new Model\Role();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Role());
                	$model->setHydrator(new Mapper\RoleHydrator());
                	return $model;
                },
                'application_model_bank' => function ($sm) {
                	$model = new Model\Bank();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Bank());
                	$model->setHydrator(new Mapper\BankHydrator());
                	return $model;
                },
                'application_model_credit_card' => function ($sm) {
                    $model = new Model\CreditCard();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    $model->setEntityPrototype(new \Application\Entity\CreditCard());
                    $model->setHydrator(new Mapper\CreditCardHydrator());
                    return $model;
                },
                'application_model_credit_card_provider' => function ($sm) {
                    $model = new Model\CreditCardProvider();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    $model->setEntityPrototype(new \Application\Entity\CreditCardProvider());
                    $model->setHydrator(new Mapper\CreditCardProviderHydrator());
                    return $model;
                },
                'application_model_business_loan_package' => function ($sm) {
                	$model = new Model\Loan();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Loan());
                	$model->setHydrator(new Mapper\LoanHydrator());
                	return $model;
                },
                'application_model_property_loan_package' => function ($sm) {
                	$model = new Model\PropertyLoanPackage();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\PropertyLoanPackage());
                	$model->setHydrator(new Mapper\PropertyLoanPackageHydrator());
                	return $model;
                },
                'application_model_property_loan_ref' => function ($sm) {
                	$model = new Model\PropertyLoanRef();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\PropertyLoanRef());
                	$model->setHydrator(new Mapper\PropertyLoanRefHydrator());
                	return $model;
                },
                'application_model_property_loan_bank' => function ($sm) {
                	$model = new Model\PropertyLoanBank();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\PropertyLoanBank());
                	$model->setHydrator(new Mapper\PropertyLoanBankHydrator());
                	return $model;
                },
                'application_model_business_loan' => function ($sm) {
                	$model = new Model\PersonalLoan();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\PersonalLoan());
                	$model->setHydrator(new Mapper\PersonalLoanHydrator());
                	return $model;
                },
                'application_model_bank_account_package' => function ($sm) {
                	$model = new Model\BankAccountPackage();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\BankAccountPackage());
                	$model->setHydrator(new Mapper\BankAccountPackageHydrator());
                	return $model;
                },
                'application_model_design' => function ($sm) {
                    $model = new Model\Design();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    $model->setEntityPrototype(new \Application\Entity\Design());
                    $model->setHydrator(new Mapper\DesignHydrator());
                    return $model;
                },
                'application_model_bank_account' => function ($sm) {
                	$model = new Model\BankAccount();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\BankAccount());
                	$model->setHydrator(new Mapper\BankAccountHydrator());
                	return $model;
                },
                'application_model_property_loan' => function ($sm) {
                	$model = new Model\PropertyLoan();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\PropertyLoan());
                	$model->setHydrator(new Mapper\PropertyLoanHydrator());
                	return $model;
                },
                'application_model_property_cost_out_play' => function ($sm) {
                    $model = new Model\PropertyCostOutPlay();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    $model->setEntityPrototype(new \Application\Entity\PropertyCostOutPlay());
                    $model->setHydrator(new Mapper\PropertyCostOutPlayHydrator());
                    return $model;
                },
                'application_model_referral' => function ($sm) {
                	$model = new Model\Referral();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Referral());
                	$model->setHydrator(new Mapper\ReferralHydrator());
                	return $model;
                },
                'application_model_menu' => function ($sm) {
                	$model = new Model\Menu();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Menu());
                	$model->setHydrator(new Mapper\MenuHydrator());
                	return $model;
                },
                'application_model_menu_group' => function ($sm) {
                	$model = new Model\MenuGroup();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\MenuGroup());
                	$model->setHydrator(new Mapper\MenuGroupHydrator());
                	return $model;
                },
                'application_model_category' => function ($sm) {
                	$model = new Model\Category();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Category());
                	$model->setHydrator(new Mapper\CategoryHydrator());
                	return $model;
                },
                'application_model_bank_interest_rate' => function ($sm) {
                    $model = new Model\BankInterestRate();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    $model->setEntityPrototype(new \Application\Entity\BankInterestRate());
                    $model->setHydrator(new Mapper\BankInterestRateHydrator());
                    return $model;
                },
                'application_model_post' => function ($sm) {
                	$model = new Model\Post();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Post());
                	$model->setHydrator(new Mapper\PostHydrator());
                	return $model;
                },
                'application_model_media' => function ($sm) {
                	$model = new Model\Media();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Media());
                	$model->setHydrator(new Mapper\MediaHydrator());
                	return $model;
                },
                'application_model_media_import' => function ($sm) {
                	$model = new Model\MediaImport();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\MediaImport());
                	$model->setHydrator(new Mapper\MediaImportHydrator());
                	return $model;
                },
                'application_model_page' => function ($sm) {
                	$model = new Model\Page();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Page());
                	$model->setHydrator(new Mapper\PageHydrator());
                	return $model;
                },
                'application_model_slider' => function ($sm) {
                	$model = new Model\Slider();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Slider());
                	$model->setHydrator(new Mapper\SliderHydrator());
                	return $model;
                },
                'application_model_testimonial' => function ($sm) {
                	$model = new Model\Testimonial();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Testimonial());
                	$model->setHydrator(new Mapper\TestimonialHydrator());
                	return $model;
                },
                'application_model_infographic' => function ($sm) {
                	$model = new Model\Infographic();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Infographic());
                	$model->setHydrator(new Mapper\InfographicHydrator());
                	return $model;
                },
                'application_model_faq' => function ($sm) {
                	$model = new Model\Faq();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Faq());
                	$model->setHydrator(new Mapper\FaqHydrator());
                	return $model;
                },
                'application_model_widget' => function ($sm) {
                	$model = new Model\Widget();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Widget());
                	$model->setHydrator(new Mapper\WidgetHydrator());
                	return $model;
                },
                'application_model_subscribe' => function ($sm) {
                	$model = new Model\Subscribe();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                	$model->setEntityPrototype(new \Application\Entity\Subscribe());
                	$model->setHydrator(new Mapper\SubscribeHydrator());
                	return $model;
                },
                'application_model_business_loan_eligibility' => function ($sm) {
                    $model = new Model\BusinessLoanEligibility();
                    $model->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                    $model->setEntityPrototype(new \Application\Entity\BusinessLoanEligibility());
                    $model->setHydrator(new Mapper\BusinessLoanEligibilityHydrator());
                    return $model;
                },
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Mynamespace' => __DIR__ . '/../../vendor/Mynamespace',
                ),
            ),
        );
    }
}
