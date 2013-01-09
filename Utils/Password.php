<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Utils;

use Nette\Utils\Strings;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 *
 * @property-write string $algorithm
 * @property-write string $password
 * @property string $hash
 */
class Password extends \Nette\Object
{

	const DELIMITER = '$';

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $salt;

	/**
	 * @var string
	 */
	private $algorithm = 'sha256';



	/**
	 * @param string|NULL $hash
	 */
	public function __construct($hash = NULL)
	{
		if ($hash) {
			$this->setHash($hash);
		}
	}



	/**
	 * @param string|NULL $password
	 * @return string
	 */
	public function setPassword($password = NULL)
	{
		$this->salt = Strings::random();
		$password = $password === NULL ? Strings::random() : $password;
		$this->password = hash($this->algorithm, $this->salt . $password);
		return $password;
	}



	/**
	 * @param string $algorithm
	 * @return Password
	 */
	public function setAlgorithm($algorithm)
	{
		$this->algorithm = $algorithm;
		return $this;
	}



	/**
	 * @param string $password
	 * @return string
	 */
	public function getHash($password = NULL)
	{
		$hash = $this->algorithm . self::DELIMITER
			. $this->salt . self::DELIMITER;

		if ($password === NULL) {
			$hash .= $this->password;
		} else {
			$hash .= hash($this->algorithm, $this->salt . $password);
		}

		return $hash;
	}



	/**
	 * @param string $hash
	 * @return Password
	 */
	public function setHash($hash)
	{
		$parts = explode(self::DELIMITER, $hash);
		$this->algorithm = $parts[0];
		$this->salt = $parts[1];
		$this->password = $parts[2];
		return $this;
	}



	/**
	 * @param Password|string $value
	 * @return bool
	 */
	public function compare($value)
	{
		if ($value instanceof Password) {
			return $this->getHash() === $value->getHash();
		} else {
			return $this->getHash() === $this->getHash($value);
		}
	}

}