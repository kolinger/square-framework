<?php

namespace Square\Doctrine;

/**
 * @author Tomáš Kolinger
 */
class Cache extends \Doctrine\Common\Cache\CacheProvider
{
	/** @var \Nette\Caching\IStorage */
	private $storage;

	/**
	 * @param \Nette\Caching\IStorage
	 */
	public function __construct(\Nette\Caching\IStorage $storage)
	{
		$this->storage = new \Nette\Caching\Cache($storage, 'Square.Doctrine');
	}

	/**
	 * @param string
	 * @return FALSE|string
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
	 * @param string
	 * @return boolean
	 */
	protected function doContains($id)
	{
		if ($this->storage->load($id) !== NULL) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param string
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	protected function doSave($id, $data, $lifeTime = FALSE)
	{
		$dependencies = array(
			\Nette\Caching\Cache::TAGS => array('doctrine'),
		);

		if ($lifeTime) {
			$dependencies[\Nette\Caching\Cache::EXPIRE] = time() + $lifeTime;
		}

		$this->storage->save($id, $data, $dependencies);
		return TRUE;
	}

	/**
	 * @param string
	 * @return boolean
	 */
	protected function doDelete($id)
	{
		$this->storage->save($id, NULL);
		return TRUE;
	}

	/**
	 * @return boolean
	 */
	protected function doFlush()
	{
		$this->storage->clean(array(
			\Nette\Caching\Cache::ALL => TRUE
		));
		return TRUE;
	}

	/**
	 * @return NULL|array
	 */
	protected function doGetStats()
	{
		return NULL;
	}
}