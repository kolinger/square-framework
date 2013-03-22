<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Forms\Controls;

use Nette\Forms\Container;
use Nette\Forms\Controls\TextArea;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Editor extends TextArea
{

	/**
	 * @param string $label
	 * @param int $cols
	 * @param int $rows
	 */
	public function __construct($label = NULL, $cols = NULL, $rows = NULL)
	{
		parent::__construct($label, $cols, $rows);

		$this->control->data['editor'] = 1;
	}



	/**
	 * @param string $name
	 */
	public static function register($name = 'addEditor')
	{
		Container::extensionMethod($name, function (Container $container, $name, $label = NULL, $cols = NULL, $rows = NULL) {
			return $container[$name] = new Editor($label, $cols, $rows);
		});
	}

}
