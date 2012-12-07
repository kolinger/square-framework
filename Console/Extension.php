<?php

namespace Square\Console;

/**
 * @author Tomáš Kolinger
 */
class Extension extends \Nette\Config\CompilerExtension
{
	/** @var array */
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
	 * @param \Nette\DI\Container
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
	 * @param array
	 * @param \Nette\DI\Container
	 * @param \Symfony\Component\Console\Helper\HelperSet
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
