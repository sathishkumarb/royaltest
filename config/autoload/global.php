<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
 define('BASE_URL', 'http://'.@$_SERVER['SERVER_NAME']);
 define('DOCUMENT_ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
 define('WEBSITE_FULL_ADDRESS', BASE_URL);
return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=product_db;host=localhost',
		'username'       => 'root',
        'password'       => '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
	'pathInfo' => array(
        'ROOTPATH'         => $_SERVER['DOCUMENT_ROOT'],
		'group_img_path' =>$_SERVER['DOCUMENT_ROOT']."/public/datagd/group/",
    ),
	'image_folders' => array(
        'group'         => "datagd/group/",	
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
	'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'myapp',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            array(
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
            ),
        ),
    ),
	'locale' => array(
		'default' => 'ar_AE',
		'available'     => array(			 
			'ar_AE' => 'Arabic',			
			'en_US' => 'English',		
		),
	),
);
