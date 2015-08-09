<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function _initCustomBootstrap(){
	/** Zend Db Initialization */
	$registry=Zend_Registry::getInstance();
	$config=array('host'		=>'localhost',
                'username'	=>'nizam',
				'password'  =>'ajith786',
				'dbname'	=>'fztest1'
               );
	$DB=new Zend_Db_Adapter_Pdo_Mysql($config);
	$registry->set('DB', $DB);
	$registry->set('onlineusers',array());
	
	}
	public function _initCustomRoute(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$router->addRoute(
				'user',
				new Zend_Controller_Router_Route(':userid',
						array('controller' => 'profile',
								'action' => 'index')));
		/*$router->addRoute(
				'user1',
				new Zend_Controller_Router_Route('/:userid/:tab/',
						array('controller' => 'profile',
								'action' => 'index')));*/
		$router->addRoute(
				'login',
				new Zend_Controller_Router_Route('login/*',
						array('controller' => 'index',
								'action' => 'login'))
		);
	}
	protected function _initAutoloaders()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->setFallbackAutoloader(true);
	
		$default_loader = new Zend_Application_Module_Autoloader(array(
				'namespace' => '',
				'basePath'  => APPLICATION_PATH
		));
	}

}

