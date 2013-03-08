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
class Configurator extends \Nette\Config\Configurator
{

	/**
	 * @return \Nette\Config\Compiler
	 */
	protected function createCompiler()
	{
		$compiler = parent::createCompiler();

		$compiler->addExtension(SquareExtension::NAME, new SquareExtension);
		$compiler->addExtension(\Square\Doctrine\Extension::NAME, new \Square\Doctrine\Extension);
		$compiler->addExtension(\Square\Console\Extension::NAME, new \Square\Console\Extension);

		return $compiler;
	}

}
