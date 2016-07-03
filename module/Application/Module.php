<?php
namespace Application;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();		 
		$sm->get('translator');
        $this->initTranslator($e);
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
	// in Application/Module.php
	public function getControllerPluginConfig()
	{
		return array(
			'factories' => array(
				'GenericPlugin' => function($sm) {
					return new Controller\Plugin\GenericPlugin();
				}
			 )
		  ); 
	}
	public function getServiceConfig()
    {
        return array(
            'factories' => array(
				//session ends
            ),
        );
    }
	public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
				//session ends
            ),
        );
    }	 
	protected function initTranslator(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        // Zend\Session\Container
        $session = New Container('language');
		if(!isset($session->language)||$session->language==''){
			$session->language = 'en_US';
		}
        $translator = $serviceManager->get('translator');
        $translator
            ->setLocale($session->language)
            ->setFallbackLocale('en_US');
    }
}

