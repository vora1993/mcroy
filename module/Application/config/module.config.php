<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'frontend',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'application'  => Controller\IndexController::class
        ),
    ),
    'admin' => array(
        'use_admin_layout' => true,
        'admin_layout_template' => 'layout/backend',
        'admin_login_template'  => 'layout/login',
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'            => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index'  => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'                => __DIR__ . '/../view/error/404.phtml',
            'error/index'              => __DIR__ . '/../view/error/index.phtml',
            'content'                  => __DIR__ . '/../view/partial/content.phtml',
            'backend_sidebar'          => __DIR__ . '/../view/partial/backend-sidebar.phtml',
            'backend_header'           => __DIR__ . '/../view/partial/backend-header.phtml',
            'frontend_header'          => __DIR__ . '/../view/partial/frontend-header.phtml',
            'backend_footer'           => __DIR__ . '/../view/partial/backend-footer.phtml',
            'frontend_auth'            => __DIR__ . '/../view/partial/frontend-auth.phtml',
            'frontend_footer'          => __DIR__ . '/../view/partial/frontend-footer.phtml',
            'frontend_sub_footer'      => __DIR__ . '/../view/partial/frontend-sub-footer.phtml',
            'frontend_user_sidebar'    => __DIR__ . '/../view/partial/frontend-user-sidebar.phtml',
            'loan_header'              => __DIR__ . '/../view/partial/frontend-loan-header.phtml',
            'loan_footer'              => __DIR__ . '/../view/partial/frontend-loan-footer.phtml',
            'home_loan_step_1'         => __DIR__ . '/../view/partial/home-loan/step-1.phtml',
            'home_loan_step_2'         => __DIR__ . '/../view/partial/home-loan/step-2.phtml',
            'home_loan_step_3'         => __DIR__ . '/../view/partial/home-loan/step-3.phtml',
            'home_loan_step_4'         => __DIR__ . '/../view/partial/home-loan/step-4.phtml',
            'home_loan_step_5'         => __DIR__ . '/../view/partial/home-loan/step-5.phtml',
            'property_loan_step_1'     => __DIR__ . '/../view/partial/property-loan/step-1.phtml',
            'property_loan_step_2'     => __DIR__ . '/../view/partial/property-loan/step-2.phtml',
            'property_loan_step_3'     => __DIR__ . '/../view/partial/property-loan/step-3.phtml',
            'property_loan_step_4'     => __DIR__ . '/../view/partial/property-loan/step-4.phtml',
            'property_loan_step_5'     => __DIR__ . '/../view/partial/property-loan/step-5.phtml',
            'mortgage_calculator'      => __DIR__ . '/../view/partial/mortgage-calculator.phtml',
            'business_loan_calculator' => __DIR__ . '/../view/partial/business-loan-calculator.phtml',
            'crowfunding_repayment'    => __DIR__ . '/../view/partial/crowfunding-repayment-calculator.phtml',
            'credit_card_more_info'      => __DIR__ . '/../view/partial/credit-card-more-info.phtml',
            'credit_card_navigation'      => __DIR__ . '/../view/partial/credit-card-navigation.phtml',
            'credit_card_filters'      => __DIR__ . '/../view/partial/credit-card-filters.phtml',
            'credit_card_details'      => __DIR__ . '/../view/partial/credit-card-details.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
