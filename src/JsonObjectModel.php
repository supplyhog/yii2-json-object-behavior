<?php
/**
 * Created by PhpStorm.
 * User: wil
 * Date: 2/1/16
 * Time: 10:51 AM
 */

namespace supplyhog\JsonObjectBehavior;

use yii\helpers\Json;
use yii\base\Model;

abstract class JsonObjectModel extends Model
{
	/**
	 * The __toString method allows a class to decide how it will react when it is converted to a string.
	 *
	 * @return string
	 * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	 */
	function __toString()
	{
		return Json::encode($this);
	}
}