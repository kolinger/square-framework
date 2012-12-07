<?php

namespace Square\Config;

/**
 * @author Tomáš Kolinger
 */
class Configurator extends \Nette\Config\Configurator
{
	/**
	 * @return \Nette\Config\Compiler
	 */
	protected function createCompiler()
	{
		$compiler = parent::createCompiler();

		$compiler->addExtension('doctrine', new \Square\Doctrine\Extension);
		$compiler->addExtension('console', new \Square\Console\Extension);

		return $compiler;
	}
}
