<?php

namespace supplyhog\JsonObjectBehavior\tests\models;

use supplyhog\JsonObjectBehavior\JsonObjectBehavior;
use yii\db\ActiveRecord;

class TestModel extends ActiveRecord
{
	public $field;

	public $fieldArray;

	public function behaviors()
	{
		return [
			'field' => [
				'class' => JsonObjectBehavior::className(),
				'attribute' => 'field',
				'objectClass' => JsonTest::className(),
				'init' => true,
			],
			'fields' => [
				'class' => JsonObjectBehavior::className(),
				'attribute' => 'fieldArray',
				'array' => true,
				'init' => true,
				'default' => [
					'class' => JsonTest::className(),
					'hello' => 'default test',
				]
			],
		];
	}
}