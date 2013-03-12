<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Thumbnails;

use Nette\Image;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Thumbnail extends \Nette\Object
{

	const THUMBNAILS_DIR = 'thumbnails';

	/**
	 * @var string
	 */
	private $file;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var int
	 */
	private $width;

	/**
	 * @var int
	 */
	private $height;

	/**
	 * @var int
	 */
	private $flags = Image::EXACT;



	/**
	 * @param string $file
	 * @param string $url
	 */
	public function __construct($file, $url)
	{
		$this->file = $file;
		$this->url = $url;
	}



	/**
	 * @param int $flags
	 */
	public function setFlags($flags)
	{
		$this->flags = $flags;
	}



	/**
	 * @return int
	 */
	public function getFlags()
	{
		return $this->flags;
	}



	/**
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}



	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}



	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}



	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}



	/**
	 * @param int $height
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	}



	/**
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}



	/**
	 * @param int $width
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}



	/**
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}



	/**
	 * @return string
 	 */
	public function getImageUrl()
	{
		$fileName = basename($this->file);

		if (!$this->width && !$this->height) {
			return $this->url . '/' . $fileName;
		}

		$parts = explode('.', $fileName);
		$extension = end($parts);
		$name = substr($fileName, 0, strlen($fileName) - (strlen($extension) + 1));
		$fileName = $name . '_' . trim((string)$this->width) . 'x' . trim((string)$this->height) . '.' . $extension;
		$dir = dirname($this->file) . '/' . static::THUMBNAILS_DIR;
		$file = $dir . '/' . $fileName;

		if (!file_exists($file)) {
			if (!is_dir($dir)) {
				mkdir($dir);
			}
			$image = Image::fromFile($this->file);
			$image->resize($this->width, $this->height, $this->flags);
			$image->save($file);
		}

		return $this->url . '/' . static::THUMBNAILS_DIR . '/' .$fileName;
	}



	/**
	 * @param string $file
	 * @param string $url
	 * @param int $width
	 * @param int $height
	 * @param int $flags
	 * @return string
	 */
	public static function getThumbnail($file, $url, $width = NULL, $height = NULL, $flags = NULL)
	{
		$me = new self($file, $url);
		$me->setWidth($width);
		$me->setHeight($height);
		if ($flags != NULL) {
			$me->setFlags($flags);
		}
		return $me->getImageUrl();
	}

}