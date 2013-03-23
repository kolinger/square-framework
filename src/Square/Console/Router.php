<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Console;

use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\Object;
use Symfony\Component\Console\Application;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Router extends Object implements IRouter
{

	/**
	 * @var Application
	 */
	private $console;



	/**
	 * @param Application $console
	 */
	public function __construct(Application $console)
	{
		$this->console = $console;
	}



	/**
	 * @param IRequest $httpRequest
	 * @return Request|NULL
	 */
	public function match(IRequest $httpRequest)
	{
		if (PHP_SAPI == 'cli') {
			return new Request('Nette:Micro', 'CLI', array('callback' => callback($this->console, 'run')));
		}
		return NULL;
	}



	/**
	 * @param Request $appRequest
	 * @param Url $refUrl
	 * @return NULL|string
	 */
	public function constructUrl(Request $appRequest, Url $refUrl)
	{
		return NULL;
	}

}
