<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Config;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class SquareExtension extends \Nette\Config\CompilerExtension
{

	/**
	 * @var array
	 */
	private $defaults = array(
		'namespaces' => array(
			'App' => 10,
		),
		'modules' => array(),
	);



	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$container->getDefinition('nette.presenterFactory')
			->setClass('Square\Application\PresenterFactory', array($config['namespaces']));

		foreach ($config['modules'] as $extension) {
			$this->compiler->addExtension($extension::NAME, new $extension);
		}
	}

}