<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Console;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Router extends \Nette\Object implements \Nette\Application\IRouter
{

	/**
	 * @var \Symfony\Component\Console\Application
	 */
	private $console;



	/**
	 * @param \Symfony\Component\Console\Application $console
	 */
	public function __construct(\Symfony\Component\Console\Application $console)
	{
		$this->console = $console;
	}



	/**
	 * @param \Nette\Http\IRequest $httpRequest
	 * @return \Nette\Application\Request|NULL
	 */
	public function match(\Nette\Http\IRequest $httpRequest)
	{
		if (PHP_SAPI == 'cli') {
			return new \Nette\Application\Request('Nette:Micro', 'CLI', array('callback' => callback($this->console, 'run')));
		}
		return NULL;
	}



	/**
	 * @param \Nette\Application\Request $appRequest
	 * @param \Nette\Http\Url $refUrl
	 * @return NULL|string
	 */
	public function constructUrl(\Nette\Application\Request $appRequest, \Nette\Http\Url $refUrl)
	{
		return NULL;
	}

}
