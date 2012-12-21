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
class Helper extends \Nette\Object
{

	public function __construct()
	{
		throw new \Nette\StaticClassException;
	}



	/**
	 * @param \Exception $e
	 * @return \Doctrine\DBAL\DBALException|\Exception|\PDOException
	 * @throws EmptyValueException
	 * @throws DuplicateEntryException
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function convertException(\Exception $e)
	{
		if ($e instanceof \Doctrine\DBAL\DBALException) {
			$pe = $e->getPrevious();
			if ($pe instanceof \PDOException) {
				$info = $pe->errorInfo;
			} else {
				throw new \Nette\InvalidArgumentException('Not supported DBAL exception type', 0, $e);
			}
		} elseif ($e instanceof \PDOException) {
			$info = $e->errorInfo;
		} else {
			return $e;
		}

		if ($info[0] == 23000 && $info[1] == 1062) { // unique fail
			$key = preg_match('#for key \'([a-z0-9\-_]*)\'#i', $e->getMessage(), $matches) ? $matches[1] : NULL;
			throw new \Square\Model\DuplicateEntryException($e->getMessage(), $key, $e);
		} elseif ($info[0] == 23000 && $info[1] == 1048) { // notnull fail
			// @todo convert table column name to entity column name
			$name = substr($info[2], strpos($info[2], "'") + 1);
			$name = substr($name, 0, strpos($name, "'"));
			throw new \Square\Model\EmptyValueException($e->getMessage(), $name, $e);
		} else {
			return $e;
		}
	}

}