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
class DuplicateEntryException extends \Exception
{

	/**
	 * @var string
	 */
	private $key;



	/**
	 * @param string $message
	 * @param string|NULL $key
	 * @param \Exception $parent
	 */
	public function __construct($message, $key = NULL, \Exception $parent = NULL)
	{
		parent::__construct($message, 0, $parent);
		$this->key = $key;
	}



	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

}
