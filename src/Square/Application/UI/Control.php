<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Application\UI;

use Nette\Application\UI\Control as NetteControl;
use Nette\Templating\ITemplate;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Control extends NetteControl
{

	/**
	 * @param string $class
	 * @return ITemplate
	 */
	public function createTemplate($class = 'Square\Templating\FileTemplate')
	{
		$template =  parent::createTemplate($class);
		$template->setTranslator(callback($this->getPresenter()->getTranslator(), 'translate'));
		return $template;
	}

}