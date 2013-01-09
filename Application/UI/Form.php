<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Application\UI;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Form extends \Nette\Application\UI\Form
{

	public function __construct()
	{
		parent::__construct();
		$this->setup();
		$this->onSuccess[] = callback($this, 'process');
	}



	protected function setup()
	{
	}


	/**
	 * @param Form $form
	 */
	public function process(self $form)
	{
	}

}