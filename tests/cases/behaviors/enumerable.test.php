<?php

/**
 * Test cases for EnumerableBehavior.
 *
 * @filesource
 * @author Vincent Bonmalais <vbonmalais@gmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */

App::import('Behavior', 'Enumerable.Enumerable');

/**
 * Basic model to load EnumBehavior
 *
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class EnumerableJob extends CakeTestModel {
	public $actsAs = array('Enumerable.Enumerable');
}

/**
 * List of test cases for EnumerableBehavior.
 * 
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class EnumerableTestCase extends CakeTestCase {

	/**
	 * List of fixtures.
	 * 
	 * Use jobs 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $fixtures = array('plugin.enumerable.enumerable_job');

	function startCase() {
		$this->Enum =& new EnumerableBehavior();
		$this->Job =& new EnumerableJob();
		$this->Enum->setup($this->Job, array(
			'cache' => true,
			'cacheName' => 'default'
		));
		parent::startCase();
	}

	/**
	 * Test find.
	 * 
	 * A simple verification.
	 * 
	 * @return void
	 * @access public
	 */
	function testFind() {
		$expected = array(
			1 => 'Manager',
			2 => 'Teacher',
			3 => 'Student'
		);
		$result = $this->Job->find('list');
		$this->assertEqual($expected, $result);
	}

	/**
	 * Test enumAll.
	 * 
	 * @return void
	 * @access public
	 */
	function testEnumAll() {
		$expected = array(
			1 => 'Manager',
			2 => 'Teacher',
			3 => 'Student'
		);
		$result = $this->Enum->enumAll($this->Job);
		$this->assertEqual($expected, $result);
	}

	/**
	 * Test enum.
	 * 
	 * @return void
	 * @access public
	 */
	function testEnum() {
		$job =& $this->Job;
		$result = $this->Enum->enum($job, 'Manager');
		$this->assertEqual(1, $result, 'Manager value.');

		$result = $this->Enum->enum($job, 'Teacher');
		$this->assertEqual(2, $result, 'Teacher value.');

		$result = $this->Enum->enum($job, 'Student');
		$this->assertEqual(3, $result, 'Student value.');

		$expected = 'Manager';
		$result = $this->Enum->enum($job, 1);
		$this->assertEqual($expected, $result, 'Should return Manager.');

		$result = $this->Enum->enum($job, '1');
		$this->assertFalse($result, 'If no value exist, this should return false.');
	}
	
	/**
	 * Test enum with array of keys
	 * 
	 * @return void
	 * @access public
	 */
	function testEnumMultipleParam() {
		$job =& $this->Job;

		$expected = array(1, 2);
		$input = array('Manager', 'Teacher');
		$result = $this->Enum->enum($job, $input);
		$this->assertEqual($expected, $result, 'Multiple entry through array.');

		$expected = array('Manager', 'Teacher');
		$input = array(1, 2);
		$result = $this->Enum->enum($job, $input);
		$this->assertEqual($expected, $result, 'Multiple entry through array. (reverse)');
	}
	
	/**
	 * Test Setup method.
	 * 
	 * @return void
	 * @access public
	 */
	function testSetup() {
		$this->expectError();
		$this->Enum->setup($this->Job, array(
			'fieldList' => array('id', 'name', 'description')
		));
	}
}