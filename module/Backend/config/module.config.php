<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'admin'              => Backend\Controller\BackendController::class,
            'user'               => Backend\Controller\UserController::class,
            'setting'            => Backend\Controller\SettingController::class,
            'bank'               => Backend\Controller\BankController::class,
            'business_loan'      => Backend\Controller\BusinessLoanController::class,
            'admin_bank_account' => Backend\Controller\BankAccountController::class,
            'factoring'          => Backend\Controller\FactoringController::class,
            'funding'            => Backend\Controller\FundingController::class,
            'property_loan'      => Backend\Controller\PropertyLoanController::class,
            'admin_page'         => Backend\Controller\PageController::class,
            'slider'             => Backend\Controller\SliderController::class,
            'testimonial'        => Backend\Controller\TestimonialController::class,
            'post'               => Backend\Controller\PostController::class,
            'media'              => Backend\Controller\MediaController::class,
            'infographic'        => Backend\Controller\InfographicController::class,
            'application_loan'   => Backend\Controller\ApplicationLoanController::class,
            'crm'                => Backend\Controller\CrmController::class,
            'credit_cards'       => Backend\Controller\CreditCardsController::class,
		)
	),
	'router' => array(
		'routes' => array(
			'admin' => array(
				'type' => 'Segment',
                'priority' => 1000,
				'options' => array(
					'route' => '/admin',
					'defaults' => array(
						'controller' => 'admin',
						'action' => 'index'
					),
				),
                'may_terminate' => true,
                'child_routes' => array(
                    'user' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/user[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'user',
        						'action' => 'index'
        					),
        				),
        			),
                    'setting' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/setting[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'setting',
        						'action' => 'index'
        					),
        				),
        			),
                    'bank' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/bank[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'bank',
        						'action' => 'index'
        					),
        				),
        			),
                    'credit_cards' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/credit-cards[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'controller' => 'credit_cards',
                                'action' => 'index'
                            ),
                        ),
                    ),
                    'business_loan' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/business-loan[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'business_loan',
        						'action' => 'index'
        					),
        				),
        			),
                    'admin_bank_account' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/bank-account[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'admin_bank_account',
        						'action' => 'index'
        					),
        				),
        			),
                    'factoring' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/factoring[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'factoring',
        						'action' => 'index'
        					),
        				),
        			),
                    'funding' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/peertopeer-funding[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'funding',
        						'action' => 'index'
        					),
        				),
        			),
                    'property_loan' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/property-loan[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'property_loan',
        						'action' => 'index'
        					),
        				),
        			),
                    'application_loan' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/loan-application[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'application_loan',
        						'action' => 'index'
        					),
        				),
        			),
                    'post' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/post[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'post',
        						'action' => 'index'
        					),
        				),
        			),
                    'admin_page' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/page[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'admin_page',
        						'action' => 'index'
        					),
        				),
        			),
                    'slider' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/slider[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'slider',
        						'action' => 'index'
        					),
        				),
        			),
                    'testimonial' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/testimonial[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'testimonial',
        						'action' => 'index'
        					),
        				),
        			),
                    'crm' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/crm[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'crm',
        						'action' => 'index'
        					),
        				),
        			),
                    'media' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/media[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'media',
        						'action' => 'index'
        					),
        				),
        			),
                    'infographic' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/infographic[/:action][/:id]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'controller' => 'infographic',
        						'action' => 'index'
        					),
        				),
        			),
                ),
			),
		)
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'backend' => __DIR__ . '/../view'
		),
	)
);