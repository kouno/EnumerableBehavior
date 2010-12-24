<?php

class EnumerableJobFixture extends CakeTestFixture {

	public $name = 'EnumerableJob';

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 255, 'null' => false),
		'description' => array('type' => 'string', 'length' => 255, 'null' => false)
	);

	public $records = array(
		array ('id' => 1, 'name' => 'Manager', 'description' => 'Manage'),
		array ('id' => 2, 'name' => 'Teacher', 'description' => 'Give courses'),
		array ('id' => 3, 'name' => 'Student', 'description' => 'Study')
	);
}