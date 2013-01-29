<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Doctrine;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Extension extends \Nette\Config\CompilerExtension
{

	/**
	 * @var array
	 */
	private $defaults = array(
		'entitiesDirs' => array('%appDir%'),
		'proxyDir' => '%appDir%/proxies',
		'proxyNamespace' => 'Proxies',
		'defaultRepositoryClassName' => 'Doctrine\ORM\EntityRepository',
		'connection' => array(
			'driver' => 'pdo_mysql',
			'host' => 'localhost',
			'user' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'collation' => 'utf8_czech_ci',
			'dbname' => '',
		),
	);



	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults); // TODO: prevent overwriting 'connection' section

		$container->addDefinition($this->prefix('cache'))
			->setClass('Square\Doctrine\Cache');

		$container->addDefinition($this->prefix('metadataDriver'))
			->setClass('Doctrine\ORM\Mapping\Driver\AnnotationDriver')
			->setFactory(get_called_class() . '::createAnnotationDriver', array($config['entitiesDirs']));

		$container->addDefinition($this->prefix('logger'))
			->setClass('Square\Doctrine\Logger');

		$container->addDefinition($this->prefix('configuration'))
			->setClass('Doctrine\ORM\Configuration')
			->addSetup('setMetadataCacheImpl', $this->prefix('@cache'))
			->addSetup('setQueryCacheImpl', $this->prefix('@cache'))
			->addSetup('setSQLLogger', $this->prefix('@logger'))
			->addSetup('setProxyDir', $config['proxyDir'])
			->addSetup('setProxyNamespace', $config['proxyNamespace'])
			->addSetup('setDefaultRepositoryClassName', $config['defaultRepositoryClassName'])
			->addSetup('setAutoGenerateProxyClasses', '%debugMode%')
			->addSetup('setMetadataDriverImpl', $this->prefix('@metadataDriver'));

		$container->addDefinition($this->prefix('connection'))
			->setClass('Doctrine\DBAL\Connection')
			->setFactory(get_called_class() . '::createConnection', array($config['connection']));

		$container->addDefinition($this->prefix('entityManager'))
			->setClass('Doctrine\ORM\EntityManager')
			->setFactory('Doctrine\ORM\EntityManager::create', array($this->prefix('@connection')));

		$container->addDefinition($this->prefix('entityManagerConsoleHelper'))
			->setClass('Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper')
			->addTag('consoleHelper', 'em');

		$container->addDefinition($this->prefix('connectionConsoleHelper'))
			->setClass('Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper')
			->addTag('consoleHelper', 'db');

		$container->addDefinition($this->prefix('dropSchemaToolConsoleCommand'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand')
			->addTag('consoleCommand');

		$container->addDefinition($this->prefix('updateSchemaToolConsoleCommand'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand')
			->addTag('consoleCommand');

		$container->addDefinition($this->prefix('createSchemaToolConsoleCommand'))
			->setClass('Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand')
			->addTag('consoleCommand');

		$container->addDefinition($this->prefix('metadataClearCacheConsoleCommand'))
			->setClass('Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand')
			->addTag('consoleCommand');

		$container->addDefinition($this->prefix('queryClearCacheConsoleCommand'))
			->setClass('Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand')
			->addTag('consoleCommand');
	}



	/**
	 * @param array $config
	 * @param Logger $logger
	 * @param \Doctrine\ORM\Configuration $configuration
	 * @return \Doctrine\DBAL\Connection
	 */
	public static function createConnection(array $config, Logger $logger, \Doctrine\ORM\Configuration $configuration)
	{
		$eventManager = new \Doctrine\Common\EventManager;
		$eventManager->addEventSubscriber(
			new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit($config['charset'], $config['collation'])
		);

		$connection = \Doctrine\DBAL\DriverManager::getConnection(
			$config,
			$configuration,
			$eventManager
		);

		if (\Nette\Diagnostics\Debugger::$bar) {
			\Nette\Diagnostics\Debugger::$bar->addPanel($logger);
		}
		\Nette\Diagnostics\Debugger::$blueScreen->addPanel(array($logger, 'renderException'));
		$logger->setConnection($connection);

		return $connection;
	}



	/**
	 * @param array $entitiesDirs
	 * @param Cache $cache
	 * @return \Doctrine\ORM\Mapping\Driver\AnnotationDriver
	 */
	public static function createAnnotationDriver(array $entitiesDirs, Cache $cache)
	{
		$doctrinePath = dirname(\Nette\Reflection\ClassType::from('Doctrine\ORM\Version')->getFileName());
		\Doctrine\Common\Annotations\AnnotationRegistry::registerFile($doctrinePath . '/Mapping/Driver/DoctrineAnnotations.php');

		$reader = new \Doctrine\Common\Annotations\AnnotationReader();
		$cachedReader = new \Doctrine\Common\Annotations\CachedReader($reader, $cache);

		return new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($cachedReader, $entitiesDirs);
	}

}