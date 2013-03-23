<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Doctrine;

use Doctrine\Common\Cache\CacheProvider;
use Nette\Caching\Cache as NetteCache;
use Nette\Caching\IStorage;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Cache extends CacheProvider
{

	/**
	 * @var IStorage
	 */
	private $storage;



	/**
	 * @param IStorage $storage
	 */
	public function __construct(IStorage $storage)
	{
		$this->storage = new NetteCache($storage, 'Square.Doctrine');
	}



	/**
	 * @param string $id
	 * @return string|FALSE
	 */
	protected function doFetch($id)
	{
		$content = $this->storage->load($id);
		if ($content !== NULL) {
			return $content;
		}
		return FALSE;
	}



	/**
	 * @param string $id
	 * @return bool
	 */
	protected function doContains($id)
	{
		if ($this->storage->load($id) !== NULL) {
			return TRUE;
		}
		return FALSE;
	}



	/**
	 * @param string $id
	 * @param string $data
	 * @param bool $lifeTime
	 * @return bool
	 */
	protected function doSave($id, $data, $lifeTime = FALSE)
	{
		$dependencies = array(
			NetteCache::TAGS => array('doctrine'),
		);

		if ($lifeTime) {
			$dependencies[NetteCache::EXPIRE] = time() + $lifeTime;
		}

		$this->storage->save($id, $data, $dependencies);
		return TRUE;
	}



	/**
	 * @param string $id
	 * @return bool
	 */
	protected function doDelete($id)
	{
		$this->storage->save($id, NULL);
		return TRUE;
	}



	/**
	 * @return bool
	 */
	protected function doFlush()
	{
		$this->storage->clean(array(
			NetteCache::ALL => TRUE
		));
		return TRUE;
	}



	/**
	 * @return array|null
	 */
	protected function doGetStats()
	{
		return NULL;
	}

}
