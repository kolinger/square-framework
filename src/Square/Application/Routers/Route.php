<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Application\Routers;

use Nette\Application\Routers\Route as NetteRoute;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Route extends NetteRoute
{

	/**
	 * @var array
	 */
	public static $styles = array(
		// nette
		'#' => array(
			self::PATTERN => '[^/]+',
			self::FILTER_IN => 'rawurldecode',
			self::FILTER_OUT => array(__CLASS__, 'param2path'),
		),
		'?#' => array(),
		'module' => array(
			self::PATTERN => '[a-z][a-z0-9.-]*',
			self::FILTER_IN => array(__CLASS__, 'path2presenter'),
			self::FILTER_OUT => array(__CLASS__, 'presenter2path'),
		),
		'presenter' => array(
			self::PATTERN => '[a-z][a-z0-9.-]*',
			self::FILTER_IN => array(__CLASS__, 'path2presenter'),
			self::FILTER_OUT => array(__CLASS__, 'presenter2path'),
		),
		'action' => array(
			self::PATTERN => '[a-z][a-z0-9-]*',
			self::FILTER_IN => array(__CLASS__, 'path2action'),
			self::FILTER_OUT => array(__CLASS__, 'action2path'),
		),
		'?module' => array(),
		'?presenter' => array(),
		'?action' => array(),
		// square
		'language' => array(
			self::PATTERN => '[a-z]{2}',
		),
		'?language' => array(),
	);

}