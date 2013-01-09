<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Modules;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
abstract class Module extends \Nette\Config\CompilerExtension
{

	/**
	 * Module name
	 */
	const NAME = NULL;

}