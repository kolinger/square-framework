<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Templating;

use Nette\Templating\FileTemplate as NetteFileTemplate;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class FileTemplate extends NetteFileTemplate
{

	/**
	 * @param string $message
	 * @param int $count
	 * @return string
	 */
	public function translate($message, $count = NULL)
	{
		$helpers = $this->getHelpers();
		if (isset($helpers['translate'])) {
			return $helpers['translate']->invokeArgs(array($message, $count));
		}
		return $message;
	}

}