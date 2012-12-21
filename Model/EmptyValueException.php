<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Model;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class EmptyValueException extends \Exception
{

	/**
	 * @var string
	 */
	private $column;



	/**
	 * @param string $message
	 * @param string|NULL $column
	 * @param \Exception $parent
	 */
	public function __construct($message, $column = NULL, \Exception $parent = NULL)
	{
		parent::__construct($message, 0, $parent);
		$this->column = $column;
	}



	/**
	 * @return string
	 */
	public function getColumn()
	{
		return $this->column;
	}

}