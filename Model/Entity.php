<?php

namespace Square\Model;

use Doctrine\ORM\Mapping as orm;

/**
 * @orm\MappedSuperclass
 *
 * @author Tomáš Kolinger
 *
 * @property-read int $id
 */
abstract class Entity extends \Nette\Object
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
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string
	 * @return NULL|string
	 */
	protected static function normalizeString($string)
	{
		$string = \Nette\Utils\Strings::trim($string);
		if ($string === '') {
			return NULL;
		}
		return $string;
	}
}