#yii2-json-object-behavior

##Installation
Install this extension via [composer](http://getcomposer.org/download). Add this line to your project’s composer.json

```php
supplyhog/yii2-json-object-behavior” : “dev-master”
```

##What Does It Do?

JSON string field in the database becomes usable object or array of objects on an ```yii\db\ActiveRecord```. No need to covert it back to a string for the database on saving. Just use it like a property.


##Behavior Config

```php
public function behaviors()
{
  return [
    //It is best to name your behaviors so that you can use more than one
    'field_behavior' => [
      'class' => 'supplyhog\JsonObjectBehavior\JsonObjectBehavior',
      'attribute' => 'field', //Name of the attribute that holds the JSON string
      'objectClass' => JsonObjectModel::className(), //Replace with your own class that extends JsonObjectModel
      'init' => true, // (new Model())->field will be an object
      // 'default' => [], // If you need defaults 
    ],
    'field_array_behavior' => [
      'class' => 'supplyhog\JsonObjectBehavior\JsonObjectBehavior',
      'attribute' => 'fieldArray', //Name of the attribute that holds the JSON string
      'array' => true, //This is an array of objects
      'init' => true, // (new Model())->fieldArray will be an array
      'default' => [ //Object defaults. Useful sometimes. The field value
        'class' => JsonObjectModel::className(),  //Replace with your own class that extends JsonObjectModel
        'hello' => 'world', // the attribute hello will be set to "world" if the JSON string does not have a value for it 
      ],
    ],
  ];
  }

```

##JsonObjectModel

This is provided for a base for your JSON strings to change into. It extends ```\yii\base\Model``` which is quite helpful.
It is not required, but if you use your own make sure you look at the magic ```__toString()``` in the JsonObjectModel and copy it over to yours.