<?php

namespace supplyhog\JsonObjectBehavior\tests;

use supplyhog\JsonObjectBehavior\tests\models\JsonTest;
use supplyhog\JsonObjectBehavior\tests\models\TestModel;
use yii\db\ActiveRecord;


class JsonObjectBehaviorTest extends \PHPUnit_Framework_TestCase
{

	public function testInit()
	{
		$model = new TestModel();
		$this->assertInstanceOf(JsonTest::className(), $model->field);
		$this->assertTrue(is_array($model->fieldArray));

		$model->field = null;
		$model->trigger(ActiveRecord::EVENT_INIT);
		$this->assertInstanceOf(JsonTest::className(), $model->field);
	}

	public function testBefore()
	{
		$model = new TestModel();
		$model->field->hello = 'Testing';
		$otherModels = [
			new JsonTest(['hello' => 'one']),
			new JsonTest(['hello' => 'two']),
		];
		$model->fieldArray = $otherModels;
		$model->trigger(ActiveRecord::EVENT_BEFORE_INSERT);

		$this->assertEquals('{"hello":"Testing"}', $model->field);
		$this->assertEquals('[{"hello":"one"},{"hello":"two"}]', $model->fieldArray);
	}

	public function testAfter()
	{
		$model = new TestModel();

		$model->field = '{"hello":"Testing"}';
		$model->fieldArray = '[{"hello":"one"},{"hello":"two"}]';

		$model->trigger(ActiveRecord::EVENT_AFTER_FIND);

		$this->assertInstanceOf(JsonTest::className(), $model->field);
		$this->assertEquals('Testing', $model->field->hello);

		$this->assertContainsOnlyInstancesOf(JsonTest::className(), $model->fieldArray);
		$this->assertEquals('one', $model->fieldArray[0]->hello);
		$this->assertEquals('two', $model->fieldArray[1]->hello);
	}

	public function testDefault()
	{
		$model = new TestModel();
		$model->fieldArray = '[{}]';

		$model->trigger(ActiveRecord::EVENT_AFTER_FIND);

		$this->assertContainsOnlyInstancesOf(JsonTest::className(), $model->fieldArray);
		$this->assertEquals('default test', $model->fieldArray[0]->hello);
	}
}


