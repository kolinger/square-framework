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
class SquareExtension extends CompilerExtension
{

	const NAME = 'square';

	/**
	 * @var array
	 */
	private $defaults = array(
		'namespaces' => array(),
		'modules' => array(),
	);



	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		foreach ($config['extensions'] as $extension) {
			$this->compiler->addExtension($extension::NAME, new $extension);
		}

		$container->getDefinition('nette.presenterFactory')
			->setClass('Square\Application\PresenterFactory', array(
				isset($container->parameters['appDir']) ? $container->parameters['appDir'] : NULL,
				'@container',
				$config['namespaces']
			));

		$container->getDefinition('nette.latte')
			->addSetup('Square\Thumbnails\LatteMacros::factory', array('@self'));
	}

}
