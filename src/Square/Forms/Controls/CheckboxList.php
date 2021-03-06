<?php

/**
 * This file is part of the Square CMS
 *
 * Copyright (c) 2011, 2012 Tomáš Kolinger <tomas@kolinger.name>
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Square\Forms\Controls;

use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\IControl;
use Nette\Utils\Html;


/**
 * CheckboxList
 *
 * @author    David Grudl, Jan Vlcek
 * @copyright Copyright (c) 2004, 2009 David Grudl
 * @package   Nette\Extras
 */
class CheckboxList extends BaseControl
{

	/**
	 * @var Html  separator element template
	 */
	protected $separator;

	/**
	 * @var Html  container element template
	 */
	protected $container;

	/**
	 * @var array
	 */
	protected $items = array();



	/**
	 * @param string $label
	 * @param array $items  Options from which to choose
	 */
	public function __construct($label = NULL, array $items = NULL)
	{
		parent::__construct($label);

		$this->control->type = 'checkbox';
		$this->container = Html::el();
		$this->separator = Html::el('br');

		if ($items !== NULL) {
			$this->setItems($items);
		}
	}



	/**
	 * Returns selected radio value. NULL means nothing have been checked.
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		$checked = is_array($this->value) ? array_keys(array_filter($this->value)) : NULL;
		if ($checked !== NULL) {
			$checked = array_intersect(array_keys($this->items), $checked);
		}
		return $checked;
	}



	/**
	 * Returns selected radio value. NULL means nothing have been checked.
	 *
	 * @return mixed
	 */
	public function getRawValues()
	{
		return is_array($this->value) ? array_keys(array_filter($this->value)) : NULL;
	}



	/**
	 * Sets options from which to choose.
	 *
	 * @param array $items
	 * @return CheckboxList  provides a fluent interface
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
		return $this;
	}



	/**
	 * Returns options from which to choose.
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}



	/**
	 * Returns separator HTML element template.
	 *
	 * @return Html
	 */
	public function getSeparatorPrototype()
	{
		return $this->separator;
	}



	/**
	 * Returns container HTML element template.
	 *
	 * @return Html
	 */
	public function getContainerPrototype()
	{
		return $this->container;
	}



	/**
	 * Generates control's HTML element.
	 *
	 * @param mixed $key  Specify a key if you want to render just a single checkbox
	 * @return Html
	 */
	public function getControl($key = NULL)
	{
		if ($key !== NULL && !isset($this->items[$key])) {
			return NULL;
		}

		$container = clone $this->container;
		$separator = (string)$this->separator;

		$control = parent::getControl();
		$id = $control->id;
		$name = $control->name;
		$values = $this->value === NULL ? NULL : (array) $this->getValue();
		$label = Html::el('label');

		$counter = -1;
		foreach ($this->items as $k => $val) {
			$counter++;
			if ($key !== NULL && $key != $k) continue; // intentionally ==

			$control->name = $name . '[' . $k . ']';
			$control->id = $label->for = $id . '-' . $counter;
			$control->checked = (count($values) > 0) ? in_array($k, $values) : FALSE;
			$control->value = $k;

			if ($val instanceof Html) {
				$label->setHtml($val);
			} else {
				$label->setText($this->translate($val));
			}

			if ($key !== NULL) {
				return Html::el()->add($control)->add($label);
			}

			$container->add((string) $control . (string) $label . $separator);
		}

		return $container;
	}



	/**
	 * Generates label's HTML element.
	 *
	 * @param string $caption
	 *
	 * @return Html
	 */
	public function getLabel($caption = NULL)
	{
		$label = parent::getLabel($caption);
		$label->for = NULL;
		return $label;
	}



	/**
	 * Filled validator: has been any checkbox checked?
	 *
	 * @param IControl $control
	 * @return bool
	 */
	public static function validateChecked(IControl $control)
	{
		return $control->getValue() !== NULL;
	}



	/**
	 * @param string $name
	 */
	public static function register($name = 'addCheckboxList')
	{
		Container::extensionMethod($name, function (Container $container, $name, $label = NULL, array $items = NULL) {
			return $container[$name] = new CheckboxList($label, $items);
		});
	}

}