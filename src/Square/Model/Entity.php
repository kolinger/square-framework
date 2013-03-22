<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Model;

use Doctrine\ORM\Mapping as orm;
use Nette\Object;
use Nette\Utils\Strings;


/**
 * @orm\MappedSuperclass
 *
 * @author Tomáš Kolinger <tomas@kolinger.name>
 *
 * @property-read int $id
 */
abstract class Entity extends Object
{

	/**
	 * @orm\Id
	 * @orm\GeneratedValue
	 * @orm\Column(type="integer")
	 *
	 * @var int
	 */
	private $id;



	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}



	/**
	 * @param string $string
	 * @return null|string
	 */
	protected static function normalizeString($string)
	{
		$string = Strings::trim($string);
		if ($string === '') {
			return NULL;
		}
		return $string;
	}

}
