<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Application;

use Nette\Application\PresenterFactory as NettePresenterFactory;
use Nette\DI\Container;
use Nette\Utils\Strings;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class PresenterFactory extends NettePresenterFactory
{

	/**
	 * @var array
	 */
	private $namespaces = array();



	/**
	 * @param string $baseDir
	 * @param Container $container
	 * @param array $namespaces
	 */
	public function __construct($baseDir, Container $container, $namespaces = array())
	{
		parent::__construct($baseDir, $container);
		asort($namespaces);
		$this->namespaces = $namespaces;
		$this->mapping['*'] = '\*\*Presenter';
	}



	/**
	 * @param string $presenter
	 * @return string
	 */
	public function formatPresenterClass($presenter)
	{
		$class = parent::formatPresenterClass($presenter);
		if (!class_exists($class)) {
			foreach ($this->namespaces as $namespace => $priority) {
				if (Strings::endsWith($namespace, '\\')) {
					$namespace = substr($namespace, 0, -1);
				}
				if (class_exists($namespace . '\\' . $class)) {
					$class = $namespace . '\\' . $class;
					break;
				}
			}
		}
		return $class;
	}



	/**
	 * @param string $class
	 * @return string
	 */
	public function unformatPresenterClass($class)
	{
		foreach ($this->namespaces as $namespace => $priority) {
			if (Strings::startsWith($class, $namespace)) {
				$class = substr($class, strlen($namespace) + 1);
				break;
			}
		}
		return parent::unformatPresenterClass($class);
	}

}
