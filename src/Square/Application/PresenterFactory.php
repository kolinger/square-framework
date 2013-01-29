<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Application;

use Nette\Application\InvalidPresenterException;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class PresenterFactory extends \Nette\Object implements \Nette\Application\IPresenterFactory
{

	/**
	 * @var bool
	 */
	public $caseSensitive = FALSE;

	/**
	 * @var string
	 */
	private $baseDir;

	/**
	 * @var array
	 */
	private $cache = array();

	/**
	 * @var \Nette\DI\Container
	 */
	private $container;

	/**
	 * @var array
	 */
	private $namespaces = array();



	/**
	 * @param array $namespaces
	 * @param \Nette\DI\Container $container
	 */
	public function __construct(array $namespaces, \Nette\DI\Container $container)
	{
		$this->namespaces = $namespaces;
		$this->baseDir = $container->parameters['appDir'];
		$this->container = $container;
	}



	/**
	 * @param string $name
	 * @return \Nette\Application\IPresenter
	 */
	public function createPresenter($name)
	{
		$presenter = $this->container->createInstance($this->getPresenterClass($name));
		if (method_exists($presenter, 'setContext')) {
			$this->container->callMethod(array($presenter, 'setContext'));
		}
		foreach (array_reverse(get_class_methods($presenter)) as $method) {
			if (substr($method, 0, 6) === 'inject') {
				$this->container->callMethod(array($presenter, $method));
			}
		}

		if ($presenter instanceof UI\Presenter && $presenter->invalidLinkMode === NULL) {
			$presenter->invalidLinkMode = $this->container->parameters['debugMode'] ? UI\Presenter::INVALID_LINK_WARNING : UI\Presenter::INVALID_LINK_SILENT;
		}
		return $presenter;
	}



	/**
	 * @param string $name
	 * @return string
	 * @throws InvalidPresenterException
	 */
	public function getPresenterClass(& $name)
	{
		if (isset($this->cache[$name])) {
			list($class, $name) = $this->cache[$name];
			return $class;
		}

		if (!is_string($name) || !\Nette\Utils\Strings::match($name, '#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*\z#')) {
			throw new InvalidPresenterException("Presenter name must be alphanumeric string, '$name' is invalid.");
		}

		$class = $this->formatPresenterClass($name);

		if (!class_exists($class)) {
			// alternative namespaces
			$namespaces = array();
			foreach ($this->namespaces as $namespace => $priority) {
				if (class_exists($namespace . '\\' . $class)) {
					$namespaces[$priority] = $namespace;
				}
			}

			ksort($namespaces);

			if (count($namespaces) > 0) {
				$class = reset($namespaces) . '\\' . $class;
			}

			// internal autoloading
			$file = $this->formatPresenterFile($name);
			if (is_file($file) && is_readable($file)) {
				\Nette\Utils\LimitedScope::load($file, TRUE);
			}

			if (!class_exists($class)) {
				throw new InvalidPresenterException("Cannot load presenter '$name', class '$class' was not found in '$file'.");
			}
		}

		$reflection = new \Nette\Reflection\ClassType($class);
		$class = $reflection->getName();

		if (!$reflection->implementsInterface('Nette\Application\IPresenter')) {
			throw new InvalidPresenterException("Cannot load presenter '$name', class '$class' is not Nette\\Application\\IPresenter implementor.");
		}

		if ($reflection->isAbstract()) {
			throw new InvalidPresenterException("Cannot load presenter '$name', class '$class' is abstract.");
		}

		// canonicalize presenter name
		$realName = $this->unformatPresenterClass($class);
		if ($name !== $realName) {
			if ($this->caseSensitive) {
				throw new InvalidPresenterException("Cannot load presenter '$name', case mismatch. Real name is '$realName'.");
			} else {
				$this->cache[$name] = array($class, $realName);
				$name = $realName;
			}
		} else {
			$this->cache[$name] = array($class, $realName);
		}

		return $class;
	}



	/**
	 * @param string $presenter
	 * @return string
	 */
	public function formatPresenterClass($presenter)
	{
		return str_replace(':', 'Module\\', $presenter) . 'Presenter';
	}



	/**
	 * @param string $class
	 * @return string
	 */
	public function unformatPresenterClass($class)
	{
		// remove namespace
		foreach ($this->namespaces as $namespace => $priority) {
			if (\Nette\Utils\Strings::startsWith($class, $namespace)) {
				$class = substr($class, strlen($namespace) + 1);
			}
		}

		return str_replace('Module\\', ':', substr($class, 0, -9));
	}



	/**
	 * @param string $presenter
	 * @return string
	 */
	public function formatPresenterFile($presenter)
	{
		$path = '/' . str_replace(':', 'Module/', $presenter);
		return $this->baseDir . substr_replace($path, '/presenters', strrpos($path, '/'), 0) . 'Presenter.php';
	}

}