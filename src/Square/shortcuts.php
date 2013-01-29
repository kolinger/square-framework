<?php

/**
 * @param $string
 * @return string
 */
function __($string)
{
	$translator = \Nette\Environment::getContext()->getByType('Nette\Localization\ITranslator');
	return callback($translator, 'translate')->invokeArgs(func_get_args());
}