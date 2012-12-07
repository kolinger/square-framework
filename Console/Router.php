<?php

namespace Square\Console;

/**
 * @author Tomáš Kolinger
 */
class Router extends \Nette\Object implements \Nette\Application\IRouter
{
	/** @var \Symfony\Component\Console\Application */
	private $console;

	/**
	 * @param \Symfony\Component\Console\Application
	 */
	public function __construct(\Symfony\Component\Console\Application $console)
	{
		$this->console = $console;
	}

	/**
	 * @param \Nette\Http\IRequest
	 * @return \Nette\Http\IRequest|NULL
	 */
	public function match(\Nette\Http\IRequest $httpRequest)
	{
		if (PHP_SAPI == 'cli') {
			return new \Nette\Application\Request('Nette:Micro', 'CLI', array('callback' => callback($this->console, 'run')));
		}
		return NULL;
	}

	/**
	 * @param \Nette\Application\Request
	 * @param \Nette\Http\Url
	 * @return string|NULL
	 */
	public function constructUrl(\Nette\Application\Request $appRequest, \Nette\Http\Url $refUrl)
	{
		return NULL;
	}
}
