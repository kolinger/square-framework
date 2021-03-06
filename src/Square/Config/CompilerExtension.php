<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Config;

use Nette\Config\CompilerExtension as NetteCompilerExtension;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
abstract class CompilerExtension extends NetteCompilerExtension
{

	/**
	 * Module name
	 */
	const NAME = NULL;

}
