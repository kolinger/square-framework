<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Console;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Extension extends \Square\Config\CompilerExtension
{

	const NAME = 'console';

	/**
	 * @var array
	 */
	private $defaults = array(
		'name' => 'Square Command Line Interface',
		'version' => '1.0',
		'catchExceptions' => TRUE,
	);



	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$container->addDefinition($this->prefix('helperSet'))
			->setClass('Symfony\Component\Console\Helper\HelperSet')
			->setFactory(get_called_class() . '::createHelperSet');

		$container->addDefinition($this->prefix('application'))
			->setClass('Symfony\Component\Console\Application')
			->setFactory(get_called_class() . '::createApplication', array($config));

		$container->addDefinition($this->prefix('router'))
			->setClass('Square\Console\Router')
			->setAutowired(FALSE);

		$container->getDefinition('router')
			->addSetup('offsetSet', array(NULL, $this->prefix('@router')));
	}



	/**
	 * @param \Nette\DI\Container $container
	 * @return \Symfony\Component\Console\Helper\HelperSet
	 */
	public static function createHelperSet(\Nette\DI\Container $container)
	{
		$helperSet = new \Symfony\Component\Console\Helper\HelperSet;

		$helpers = $container->findByTag('consoleHelper');
		foreach ($helpers as $name => $value) {
			$helperSet->set($container->getService($name), $value);
		}

		return $helperSet;
	}



	/**
	 * @param array $config
	 * @param \Nette\DI\Container $container
	 * @param \Symfony\Component\Console\Helper\HelperSet $helperSet
	 * @return \Symfony\Component\Console\Application
	 */
	public static function createApplication(array $config, \Nette\DI\Container $container, \Symfony\Component\Console\Helper\HelperSet $helperSet)
	{
		$application = new \Symfony\Component\Console\Application($config['name'], $config['version']);
		$application->setCatchExceptions($config['catchExceptions']);
		$application->setHelperSet($helperSet);

		$commands = $container->findByTag('consoleCommand');
		foreach ($commands as $name => $value) {
			$application->add($container->getService($name));
		}

		return $application;
	}

}
