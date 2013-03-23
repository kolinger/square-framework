<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Event\Listeners\MysqlSessionInit;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Nette\Diagnostics\Debugger;
use Nette\Reflection\ClassType;
use Square\Config\CompilerExtension;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Extension extends CompilerExtension
{

	const NAME = 'doctrine';

	/**
	 * @var array
	 */
	private $defaults = array(
		'entitiesDirs' => array('%appDir%'),
		'ignoredAnnotations' => array('resource', 'privilege'),
		'proxyDir' => '%appDir%/proxies',
		'proxyNamespace' => 'Proxies',
		'defaultRepositoryClassName' => 'Doctrine\ORM\EntityRepository',
	);

	/**
	 * @var array
	 */
	private $connectionDefaults = array(
		'driver' => 'pdo_mysql',
		'host' => '127.0.0.1',
		'user' => 'root',
		'password' => '',
		'charset' => 'utf8',
		'collation' => 'utf8_czech_ci',
		'dbname' => '',
	);



	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
		$config['connection'] = array_merge($this->connectionDefaults, $config['connection']);

		$container->addDefinition($this->prefix('cache'))
			->setClass('Square\Doctrine\Cache');

		$container->addDefinition($this->prefix('metadataDriver'))
			->setClass('Doctrine\ORM\Mapping\Driver\AnnotationDriver')
			->setFactory(get_called_class() . '::createAnnotationDriver', array($config['entitiesDirs'], $config['ignoredAnnotations']));

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
	 * @param Configuration $configuration
	 * @return \Doctrine\DBAL\Connection
	 */
	public static function createConnection(array $config, Logger $logger, Configuration $configuration)
	{
		$eventManager = new EventManager;
		$eventManager->addEventSubscriber(
			new MysqlSessionInit($config['charset'], $config['collation'])
		);

		$connection = DriverManager::getConnection(
			$config,
			$configuration,
			$eventManager
		);

		if (Debugger::$bar) {
			Debugger::$bar->addPanel($logger);
		}
		Debugger::$blueScreen->addPanel(array($logger, 'renderException'));
		$logger->setConnection($connection);

		return $connection;
	}



	/**
	 * @param array $entitiesDirs
	 * @param array $ignoredAnnotations
	 * @param Cache $cache
	 * @return AnnotationDriver
	 */
	public static function createAnnotationDriver(array $entitiesDirs, array $ignoredAnnotations, Cache $cache)
	{
		$doctrinePath = dirname(ClassType::from('Doctrine\ORM\Version')->getFileName());
		AnnotationRegistry::registerFile($doctrinePath . '/Mapping/Driver/DoctrineAnnotations.php');

		$reader = new AnnotationReader();
		foreach ($ignoredAnnotations as $ignoredAnnotation) {
			$reader->addGlobalIgnoredName($ignoredAnnotation);
		}
		$cachedReader = new CachedReader($reader, $cache);

		return new AnnotationDriver($cachedReader, $entitiesDirs);
	}

}
