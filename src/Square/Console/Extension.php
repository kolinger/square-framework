<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Console;


use Nette\DI\Container;
use Square\Config\CompilerExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Extension extends CompilerExtension
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
	 * @param Container $container
	 * @return HelperSet
	 */
	public static function createHelperSet(Container $container)
	{
		$helperSet = new HelperSet;

		$helpers = $container->findByTag('consoleHelper');
		foreach ($helpers as $name => $value) {
			$helperSet->set($container->getService($name), $value);
		}

		return $helperSet;
	}



	/**
	 * @param array $config
	 * @param Container $container
	 * @param HelperSet $helperSet
	 * @return Application
	 */
	public static function createApplication(array $config, Container $container, HelperSet $helperSet)
	{
		$application = new Application($config['name'], $config['version']);
		$application->setCatchExceptions($config['catchExceptions']);
		$application->setHelperSet($helperSet);

		$commands = $container->findByTag('consoleCommand');
		foreach ($commands as $name => $value) {
			$application->add($container->getService($name));
		}

		return $application;
	}

}
