<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Application\UI;

use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\PresenterComponent;
use Nette\Caching\Cache;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Nette\InvalidStateException;
use Nette\MemberAccessException;
use Nette\Reflection\Method;
use Nette\Reflection\Property;
use Nette\Reflection\ClassType;



/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class Presenter extends \Nette\Application\UI\Presenter
{

	const CACHE_NAMESPACE = 'Square.Presenter.Autowire';

	/**
	 * @var string
	 * @persistent
	 */
	public $lang;

	/**
	 * @var \Square\Localization\GettextTranslator\Gettext
	 * @autowire
	 */
	protected $translator;

	/**
	 * @var array
	 */
	private $autowire = array();

	/**
	 * @var Container
	 */
	private $serviceLocator;

	/**
	 * @var array
	 */
	private $layouts = array();

	/**
	 * @var string
	 */
	private $title;



	protected function startup()
	{
		parent::startup();

		// translator
		if (!isset($this->lang)) {
			$this->lang = $this->getContext()->parameters["lang"];
		}
		$this->translator->setLang($this->lang);
		$this->getTemplate()->setTranslator($this->translator);
	}



	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}



	/**
	 * @param $title
	 * @param array $args
	 */
	public function setTitle($title, $args = array())
	{
		if (count($args)) {
			$this->title = $this->translator->translate($title, NULL, $args);
		} else {
			$this->title = $this->translator->translate($title);
		}
	}



	/**
	 * @param string $layout
	 */
	public function addLayout($layout)
	{
		$this->layouts[] = $layout;
	}



	/**
	 * @return array
	 */
	public function formatLayoutTemplateFiles()
	{
		$list = parent::formatLayoutTemplateFiles();
		return array_merge($list, $this->layouts);
	}



	/**
	 * @param Container $dic
	 * @throws InvalidStateException
	 * @throws MemberAccessException
	 * @throws MissingServiceException
	 */
	public function injectProperties(Container $dic)
	{
		if (!$this instanceof PresenterComponent) {
			throw new MemberAccessException('Trait ' . __TRAIT__ . ' can be used only in descendants of PresenterComponent.');
		}

		$this->serviceLocator = $dic;
		$cache = new Cache($this->serviceLocator->getByType('Nette\Caching\IStorage'), self::CACHE_NAMESPACE);
		if (($this->autowire = $cache->load($presenterClass = get_class($this))) === NULL) {
			$this->autowire = array();

			$rc = ClassType::from($this);
			$ignore = class_parents('Nette\Application\UI\Presenter') + array('ui' => 'Nette\Application\UI\Presenter');
			foreach ($rc->getProperties(Property::IS_PUBLIC | Property::IS_PROTECTED) as $prop) {
				/** @var Property $prop */
				if (in_array($prop->getDeclaringClass()->getName(), $ignore) || !$prop->hasAnnotation('autowire')) {
					continue;
				}

				if (!$type = ltrim($prop->getAnnotation('var'), '\\')) {
					throw new InvalidStateException("Missing annotation @var with typehint on $prop.");
				}

				if (!class_exists($type) && !interface_exists($type)) {
					if (substr($prop->getAnnotation('var'), 0, 1) === '\\') {
						throw new InvalidStateException("Class \"$type\" was not found, please check the typehint on $prop");
					}

					if (!class_exists($type = $prop->getDeclaringClass()->getNamespaceName() . '\\' . $type) && !interface_exists($type)) {
						throw new InvalidStateException("Neither class \"" . $prop->getAnnotation('var') . "\" or \"$type\" was found, please check the typehint on $prop");
					}
				}

				if (empty($this->serviceLocator->classes[strtolower($type)])) {
					throw new MissingServiceException("Service of type \"$type\" not found for $prop.");
				}

				// unset property to pass control to __set() and __get()
				unset($this->{$prop->getName()});
				$this->autowire[$prop->getName()] = array(
					'value' => NULL,
					'type' => ClassType::from($type)->getName()
				);
			}

			$files = array_map(function ($class) {
				return ClassType::from($class)->getFileName();
			}, array_diff(array_values(class_parents($presenterClass) + array('me' => $presenterClass)), $ignore));

			$cache->save($presenterClass, $this->autowire, array(
				$cache::FILES => $files,
			));

		} else {
			foreach ($this->autowire as $propName => $tmp) {
				unset($this->{$propName});
			}
		}
	}



	/**
	 * @param string $name
	 * @param mixed $value
	 * @throws MemberAccessException
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		if (!isset($this->autowire[$name])) {
			return parent::__set($name, $value);

		} elseif ($this->autowire[$name]['value']) {
			throw new MemberAccessException("Property \$$name has already been set.");

		} elseif (!$value instanceof $this->autowire[$name]['type']) {
			throw new MemberAccessException("Property \$$name must be an instance of " . $this->autowire[$name]['type'] . ".");
		}

		return $this->autowire[$name]['value'] = $value;
	}



	/**
	 * @param $name
	 * @throws MemberAccessException
	 * @return mixed
	 */
	public function &__get($name)
	{
		if (!isset($this->autowire[$name])) {
			return parent::__get($name);
		}

		if (empty($this->autowire[$name]['value'])) {
			$this->autowire[$name]['value'] = $this->serviceLocator->getByType($this->autowire[$name]['type']);
		}

		return $this->autowire[$name]['value'];
	}



	/**
	 * @param object $element
	 * @throws InvalidStateException
	 * @throws ForbiddenRequestException
	 */
	public function checkRequirements($element)
	{
		if ($element instanceof Method && $element->hasAnnotation('privilege')) {
			if ($element->hasAnnotation('resource')) {
				$resource = $element->getAnnotation('resource');
			} else {
				$presenter = $element->getDeclaringClass();
				if (!$presenter->hasAnnotation('resource')) {
					throw new InvalidStateException('@resource annotation must be declared');
				}
				$resource = $presenter->getAnnotation('resource');
			}

			if (!$this->getUser()->isAllowed($resource, $element->getAnnotation('privilege'))) {
				throw new ForbiddenRequestException(NULL, 403);
			}
		}

		parent::checkRequirements($element);
	}

}
