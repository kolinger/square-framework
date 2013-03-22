<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Thumbnails;

use Nette\Latte\CompileException;
use Nette\Latte\Compiler;
use Nette\Latte\MacroNode;
use Nette\Latte\Macros\MacroSet;
use Nette\Latte\PhpWriter;
use Nette\Latte\Engine;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class LatteMacros extends MacroSet
{

	/**
	 * @param \Nette\Latte\Engine
	 * @return MacroSet
	 */
	public static function factory(Engine $engine)
	{
		return static::install($engine->getCompiler());
	}



	/**
	 * @param \Nette\Latte\Compiler $compiler
	 * @return MacroSet
	 */
	public static function install(Compiler $compiler)
	{
		$me = parent::install($compiler);
		$me->addMacro('thumbnail', array($me, 'macroThumbnail'));
		return $me;
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 * @throws CompileException
	 */
	public function macroThumbnail(MacroNode $node, PhpWriter $writer)
	{
		$data = explode(',', $node->args);

		if (count($data) < 1) {
			throw new CompileException('Image file missing for thumbnail macro');
		}

		if (count($data) < 2) {
			throw new CompileException('URL missing for thumbnal macro');
		}

		$file = $data[0];
		$url = $data[1];
		$width = isset($data[2]) ? $data[2] : NULL;
		$height = isset($data[3]) ? $data[3] : NULL;
		$flags = isset($data[4]) ? $data[4] : NULL;

		return $writer->write(
			'echo %escape(Square\Thumbnails\Thumbnail::getThumbnail(
				' . $writer->formatWord($file) . ',
				' . $writer->formatWord($url) . ',
				' . $writer->formatWord($width) . ',
				' . $writer->formatWord($height) . ',
				' . $writer->formatWord($flags) . '
			));'
		);
	}

}
