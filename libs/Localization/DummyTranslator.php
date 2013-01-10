<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Localization;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 * @todo implements a real translator
 */
class DummyTranslator extends \Nette\Object implements \Nette\Localization\ITranslator
{

	/**
	 * @param string $string
	 * @param int $count
	 * @return string
	 */
	public function translate($string, $count = 0)
	{
		return $string;
	}

}