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
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\Forms\Rule;
use Nette\Forms\Rules;


/**
 * @author Tomáš Kolinger <tomas@kolinger.name>
 */
class DateTime extends BaseControl
{

	/**
	 * @param string $label
	 */
	public function __construct($label = NULL)
	{
		parent::__construct($label);

		$this->control->type = 'datetime';
	}



	/**
	 * @param string $value
	 * @return DateTime
	 */
	public function setValue($value)
	{
		// czech date format
		if (is_string($value) && preg_match('#(?<d>[0-9]{1,2})[\.-/ ]+(?<m>[0-9]{1,2})[\.-/ ]+(?<y>[0-9]{4})?(?:[\.-/ ]+(?<h>[0-9]{1,2}):(?<min>[0-9]{1,2}))?#', $value, $matches)) {
			$day = $matches['d'];
			$month = $matches['m'];
			$year = isset($matches['y']) ? $matches['y'] : date('Y');
			$hours = isset($matches['h']) ? $matches['h'] : '00';
			$minutes = isset($matches['min']) ? $matches['min'] : '00';

			$value = $year . '-' . $month . '-' . $day . ' ' . $hours . ':' . $minutes . ':00';
		}

		if ($value) {
			$this->value = \Nette\DateTime::from($value);
		}

		return $this;
	}



	/**
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{
		$control = parent::getControl();

		if ($this->value) {
			$control->value = $this->value->format('Y-m-d H:i');
		}

		list($min, $max) = $this->extractRangeRule($this->getRules());
		if ($min) {
			$this->control->min = $min->format('Y-m-d\TH:i:s');
		}
		if ($max) {
			$this->control->max = $max->format('Y-m-d\TH:i:s');
		}

		return $control;
	}



	/**
	 * @param IControl $control
	 * @return bool
	 */
	public static function validateFilled(IControl $control)
	{
		return $control->getValue() != NULL;
	}



	/**
	 * @param IControl $control
	 * @return bool
	 */
	public static function validateValid(IControl $control)
	{
		return ($control->getValue() == NULL || $control->getValue() instanceof \DateTime);
	}



	/**
	 * @author Jan Tvrdík
	 * @param Rules $rules
	 * @return array
	 */
	protected function extractRangeRule(Rules $rules)
	{
		$controlMin = $controlMax = NULL;
		foreach ($rules as $rule) {
			if ($rule->type === Rule::VALIDATOR) {
				if ($rule->operation === Form::RANGE && !$rule->isNegative) {
					$ruleMinMax = $rule->arg;
				}

			} elseif ($rule->type === Rule::CONDITION) {
				if ($rule->operation === Form::FILLED && !$rule->isNegative && $rule->control === $this) {
					$ruleMinMax = $this->extractRangeRule($rule->subRules);
				}
			}

			if (isset($ruleMinMax)) {
				list($ruleMin, $ruleMax) = $ruleMinMax;
				if ($ruleMin !== NULL && ($controlMin === NULL || $ruleMin > $controlMin)) $controlMin = $ruleMin;
				if ($ruleMax !== NULL && ($controlMax === NULL || $ruleMax < $controlMax)) $controlMax = $ruleMax;
				$ruleMinMax = NULL;
			}
		}
		return array($controlMin, $controlMax);
	}



	/**
	 * @param string $name
	 */
	public static function register($name = 'addDateTime')
	{
		Container::extensionMethod($name, function (Container $container, $name, $label = NULL) {
			return $container[$name] = new DateTime($label);
		});
	}

}
