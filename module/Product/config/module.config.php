<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Product\Controller\Product' => 'Product\Controller\ProductController',
        ),
    ),
	'controller_plugins' => array(
        'invokables' => array(
		'ResizeImage' => 'Application\Controller\Plugin\ResizeImage;',
        ),
    ),
	 // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'product' => array(
                'type'    => 'Literal',
				'priority' => 1000,
                'options' => array(
                    'route'    => '/product',
                    'defaults' => array(
						'__NAMESPACE__' => 'product\Controller',
                        'controller' => 'product',
                        'action'     => 'index',
                    ),
                ),
				'may_terminate' => true,				
				'child_routes' => array(
					'product' => array(
                        'type' => 'segment',
						'priority' => 1000,
                        'options' => array(
                            'route' => '/productadd',
							'defaults' => array(
								'__NAMESPACE__' => 'product\Controller',
								'controller' => 'product',
								'action'     => 'addProduct',
							),
                        ),					 
                    ),
					'product'=> array(
                        'type' => 'segment',
						'priority' => 1000,
                        'options' => array(
                            'route' => '/productedit',
							'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'product',
								'action'     => 'updateProduct',
							),
                        ),
                    ),
					'category'=> array(
                        'type' => 'segment',
						'priority' => 1000,
                        'options' => array(
                            'route' => '/categoryadd',
							'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'product',
								'action'     => 'categoryadd',
							),
                        ),
                    ),
					'order'=> array(
                        'type' => 'segment',
						'priority' => 1000,
                        'options' => array(
                            'route' => '/order',
							'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'product',
								'action'     => 'order',
							),
                        ),
                    ),
				),				
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'groups' => __DIR__ . '/../view',
        ),
    ),
);