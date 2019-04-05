<?php
return array(
    'controllers' => array(
		'invokables' => array(
			'frontend'            => Frontend\Controller\FrontendController::class,
            'frontend_user'       => Frontend\Controller\UserController::class,
            'personal_loan'       => Frontend\Controller\PersonalLoanController::class,
            'alternative_funding' => Frontend\Controller\AlternativeFundingController::class,
            'home_loan'           => Frontend\Controller\HomeLoanController::class,
            'refinancing'         => Frontend\Controller\RefinancingController::class,
            'refinancing_commercial'         => Frontend\Controller\RefinancingCommercialController::class,
            'blog'                => Frontend\Controller\BlogController::class,
            'page'                => Frontend\Controller\PageController::class,
            'loan_application'    => Frontend\Controller\LoanApplicationController::class,
            'bank_account'        => Frontend\Controller\BankAccountController::class,
            'credit_card'         => Frontend\Controller\CreditCardController::class,
            'design'              => Frontend\Controller\DesignController::class
		)
	),
	'router' => array(
		'routes' => array(
            'frontend' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/frontend',
                    'defaults' => array(
                        'controller'    => 'frontend',
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
            'loan_application' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/loan-application[/:action][/:seo][/:step[/:id]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'seo' => '[a-zA-Z0-9_-]*',
                        'step' => '[a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'loan_application',
		                'action' => 'index'
		            ),
                ),
            ),
            'personal_loan' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/business-term-loan[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'personal_loan',
		                'action' => 'index'
		            ),
                ),
            ),
            'bank_account' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/bank-account[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'bank_account',
		                'action' => 'index'
		            ),
                ),
            ),
            'alternative_funding' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/alternative-funding[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'alternative_funding',
		                'action' => 'index'
		            ),
                ),
            ),
            'home_loan' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/home-loan[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'home_loan',
		                'action' => 'index'
		            ),
                ),
            ),
            'refinancing' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/refinancing[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'refinancing',
		                'action' => 'index'
		            ),
                ),
            ),
            'refinancing_commercial' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/refinancing-commercial[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'refinancing_commercial',
                        'action' => 'index'
                    ),
                ),
            ),
            'credit-cards' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/credit-cards[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'credit_card',
                        'action' => 'index'
                    ),
                ),
            ),
            'frontend_user' => array(
                'type' => 'segment',
                'options' => array(
                'route' => '/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'frontend_user',
		                'action' => 'index'
		            ),
                ),
            ),
            'blog' => array(
				'type' => 'Segment',
				'options' => array(
					'route' => '/blog[/:action[/:id][-:seo]][/:paged]',
					'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
						'seo'    => '[a-zA-Z0-9_-]*',
                        'paged'  => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'blog',
						'action' => 'index'
					),
				),
			),
            'design' => array(
                'type' => 'Segment',
                'options' => array(
                'route' => '/design[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'design',
                        'action' => 'index'
                    ),
                ),
            ),
            'page' => array(
				'type' => 'segment',
                'options' => array(
                'route' => '/page[/:action][/:seo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'seo'    => '[a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'page',
		                'action' => 'index'
		            ),
                ),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'frontend' => __DIR__ . '/../view'
		),
        'template_map' => array(
            'compare'        => __DIR__ . '/../view/frontend/personal-loan/compare.phtml',
        ),
	)
);