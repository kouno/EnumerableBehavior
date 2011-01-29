<?php

/**
 * Test cases cache feature for EnumerableBehavior.
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
 * Add a throw exception flag on find to determine if the request is legitime.
 *
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class EnumerableCacheJob extends CakeTestModel {
	public $actsAs = array('Enumerable.Enumerable');
	public $useTable = 'enumerable_jobs';
	public $throwException = false;
	
	public function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		if ($this->throwException) {
			throw new Exception('Model tried to query database.');
		}
		
		return parent::find($conditions, $fields, $order, $recursive);
	}
}

/**
 * List of test cases for EnumerableBehavior.
 * 
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class EnumerableCacheTestCase extends CakeTestCase {

	/**
	 * List of fixtures.
	 * 
	 * Use jobs 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $fixtures = array('plugin.enumerable.enumerable_job');
	
	/**
	 * setUp method
	 *
	 * @access public
	 * @return void
	 */
	function setUp() {
		$this->_cacheDisable = Configure::read('Cache.disable');
		Configure::write('Cache.disable', false);

		Cache::config('enumerable_test', array('engine' => 'File', 'path' => TMP . 'tests', 'prefix' => 'cake_test_enumerable_'));
	}

	/**
	 * tearDown method
	 *
	 * @access public
	 * @return void
	 */
	function tearDown() {
		Configure::write('Cache.disable', $this->_cacheDisable);
	}

	/**
	 * startCase method
	 *
	 * @access public
	 * @return void
	 */
	function startCase() {
		$this->Enum =& new EnumerableBehavior();
		$this->Job =& new EnumerableCacheJob();
		$this->Enum->setup($this->Job, array(
			'cache' => true,
			'cacheName' => 'enumerable_test'
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
	function testEnumAllBasicCache() {
		$modelName = $this->Job->alias;
		$behaviorName = 'EnumerableBehavior';
		Cache::delete("$behaviorName.$modelName", 'enumerable_test');
		$this->Job->throwException = false;
		
		$expected = array(1 => 'Manager', 2 => 'Teacher', 3 => 'Student');
		$result = $this->Enum->enumAll($this->Job);
		$this->assertEqual($expected, $result, 'Simple check of enumAll. (set up cache)');
		
		$cache = Cache::read("$behaviorName.$modelName", 'enumerable_test');
		$this->assertFalse(empty($cache));
		$this->assertEqual($cache, $expected, 'Assert cache is created.');

		$cacheDel = Cache::delete("$behaviorName.$modelName", 'enumerable_test');
		$this->assertTrue($cacheDel, 'Assert cache is emptied (non-related to test suite - internal error).');	

		$result = $this->Enum->enumAll($this->Job, true);
		$cache = Cache::read("$behaviorName.$modelName", 'enumerable_test');
		$this->assertFalse(empty($cache));
		$this->assertEqual($cache, $expected, 'Assert cache is recreated. (regenerated properly)');
		
		$this->Job->throwException = true;
		$result = $this->Enum->enumAll($this->Job);
		$this->assertEqual($expected, $result, 'Assert no find is made when cache is enabled');
		
		$this->expectException(new Exception('Model tried to query database.'), 'Assert find is called when there is no cache.');
		$cacheDel = Cache::delete("$behaviorName.$modelName", 'enumerable_test');
		$this->Enum->enumAll($this->Job, true);
	}
}