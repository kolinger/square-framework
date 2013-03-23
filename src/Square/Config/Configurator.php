<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Config;

use Square\Console\Extension as ConsoleExtension;
use Square\Doctrine\Extension as DoctrineExtension;
use Square\Forms\Controls\CheckboxList;
use Square\Forms\Controls\DateTime;
use Square\Forms\Controls\Editor;
use Square\Forms\Controls\TagsInput;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Configurator extends \Nette\Config\Configurator
{

	/**
	 * @return \SystemContainer
	 */
	public function createContainer()
	{
		CheckboxList::register();
		DateTime::register();
		TagsInput::register();
		Editor::register();
		return parent::createContainer();
	}



	/**
	 * @return \Nette\Config\Compiler
	 */
	protected function createCompiler()
	{
		$compiler = parent::createCompiler();

		$compiler->addExtension(SquareExtension::NAME, new SquareExtension);
		$compiler->addExtension(ConsoleExtension::NAME, new ConsoleExtension);
		$compiler->addExtension(DoctrineExtension::NAME, new DoctrineExtension);

		return $compiler;
	}

}
