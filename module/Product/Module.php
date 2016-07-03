<?php
namespace Product;
use Product\Model\ProductTable;
use Product\Model\ProductAttributesTable;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
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
	public function getServiceConfig()
    {
		return array(
            'factories' => array(
				'Product\Model\ProductTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ProductTable($dbAdapter);
                    return $table;
                }, 
				'Product\Model\ProductAttributesTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ProductAttributesTable($dbAdapter);
					return $table;
                },
            ),
        );
	}
}