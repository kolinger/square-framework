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
use Nette\Forms\Controls\TextInput;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class TagsInput extends TextInput
{

	const DELIMITER = ',';



	/**
	 * @param string $label
	 */
	public function __construct($label = NULL)
	{
		parent::__construct($label);

		$this->control->autocomplete = 'off';
		$this->control->data['tags'] = 1;
	}



	/**
	 * @return array
	 */
	public function getValue()
	{
		if (strpos($this->value, static::DELIMITER) === FALSE) {
			return array();
		}
		$values = explode(static::DELIMITER, $this->value);
		return array_map('trim', $values);
	}



	/**
	 * @param array $value
	 * @return TagsInput
	 */
	public function setValue($value)
	{
		if (is_array($value)) {
			$value = implode(static::DELIMITER, $value);
		}
		parent::setValue($value);
		return $this;
	}



	/**
	 * @param string $name
	 */
	public static function register($name = 'addTagsInput')
	{
		Container::extensionMethod($name, function (Container $container, $name, $label = NULL) {
			return $container[$name] = new TagsInput($label);
		});
	}

}
