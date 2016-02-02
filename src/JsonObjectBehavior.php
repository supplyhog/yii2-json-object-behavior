<?php

namespace supplyhog\JsonObjectBehavior;

use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;


class JsonObjectBehavior extends Behavior
{

	/**
	 * Attribute on the model to target
	 * @var string
	 */
	public $attribute;

	/**
	 * The class of the objects the field will contain
	 * Note this can be overridden by the field value
	 * @var string
	 */
	public $objectClass;

	/**
	 * Initialize the field with an object or array (if array true).
	 * @var bool
	 */
	public $init = false;

	/**
	 * Instead of a single object use an array of objects
	 * @var bool
	 */
	public $array = false;

	/**
	 * The defaults to be used to create the object.
	 * These are overridden by the field value if any.
	 * @var array
	 */
	public $default = [];

	/**
	 * @var array
	 */
	protected $_beforeSaveValue = null;

	/**
	 * @inheritdoc
	 */
	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_INSERT => 'fieldToString',
			ActiveRecord::EVENT_AFTER_INSERT => 'fieldToObject',
			ActiveRecord::EVENT_BEFORE_UPDATE => 'fieldToString',
			ActiveRecord::EVENT_AFTER_UPDATE => 'fieldToObject',
			ActiveRecord::EVENT_AFTER_FIND => 'fieldToObject',
			ActiveRecord::EVENT_INIT => 'fieldInit',
		];
	}

	/**
	 * Handles initialization if enabled.
	 * @param $event
	 */
	public function fieldInit($event)
	{
		if($this->init) {
			$this->fieldToObject();
		}
	}

	/**
	 * Turn each of the fields back into strings
	 * Cache previous values in the beforeSaveValues so that they can be restored after saving the owner
	 */
	public function fieldToString()
	{
		$value = $this->owner->{$this->attribute};
		$this->_beforeSaveValue = $value;
		$this->owner->{$this->attribute} = $this->valueToString($value);
	}

	/**
	 * Take a value and make it a string
	 * @param $value
	 * @return string
	 */
	protected function valueToString($value)
	{
		if (empty($value)) {
			return $this->array ? '[]' : '{}';
		}
		return Json::encode($value);
	}

	/**
	 * Set the fields to their objects
	 */
	public function fieldToObject()
	{
		//Do we have it saved?
		if($this->_beforeSaveValue !== null) {
			$this->owner->{$this->attribute} = $this->_beforeSaveValue;
			$this->_beforeSaveValue = null;
			return;
		}
		//Nothing there defaults
		if (empty($this->owner->{$this->attribute}) || !isset($this->owner->{$this->attribute})) {
			if($this->array) {
				$this->owner->{$this->attribute} = [];
				return;
			}
			$this->owner->{$this->attribute} = $this->createObject($this->default);
			return;
		}
		//Actual saved JSON string?
		if (is_string($this->owner->{$this->attribute})) {
			$value = Json::decode($this->owner->{$this->attribute}, true);
			$this->owner->{$this->attribute} = $this->castObject($value);
			return;
		}
		//If we get here, it is due to some code running a double trigger. Just carry on. All OK.
	}

	/**
	 * Take a value and merge it with the default and return the createObject
	 * @param $value
	 * @return array|JsonObjectModel
	 */
	protected function castObject($value)
	{
		if ($this->array) {
			$map = array_map(function ($item) {
				$item = ArrayHelper::merge($this->default, $item);
				return $this->createObject($item);
			}, $value);
			if(!$map) {
				$map = [];
			}
			return $map;
		}
		$value = ArrayHelper::merge($this->default, $value);
		return $this->createObject($value);
	}

	/**
	 * Create an object out of the given array.
	 * @param array $item
	 * @return JsonObjectModel[]|JsonObjectModel
	 * @throws \yii\base\InvalidConfigException
	 */
	protected function createObject($item)
	{
		$item = !$item ? [] : $item;
		//It is possible for the item to have a class that we will use instead
		$item['class'] = isset($item['class']) ? $item['class'] : $this->objectClass;

		$obj = Yii::createObject($item);
		$obj->trigger(ActiveRecord::EVENT_AFTER_FIND);
		return $obj;
	}

}