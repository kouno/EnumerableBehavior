<?php
/**
 * Enumerable Behavior class file.
 * 
 * PHP version 4, 5
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author Vincent Bonmalais <vbonmalais@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @copyright Copyright (c) 2010, Vincent Bonmalais
 * @version 0.1
 */
/**
 * Enumerable Behavior is an easy way to manage tables which have only one purpose : replace database enumeration.
 *
 * The goal is to write less code with more meaning. 
 * 
 * Even though it would be probably better to have support for real enum type, it is not supported in CakePHP 
 * and it seems to be inconsistent between database.
 * 
 * <code>
 * <?php
 * // Good
 * $this->AssociatedModel->enum(1); // return associated name to key 1.
 * $this->AssociatedModel->enum('name'); // return key 1.
 * $this->AssociatedModel->enumAll(); //return an associative array of all records.
 * 
 * // Bad
 * $this->AssociatedModel->enum('1'); // will not work because '1' is a string
 * ?>
 * </code>
 * 
 * EnumerableBehavior have a few configuration options :
 * - fieldList : List of fields to be retrieved. (2 fields max) [required if no displayField have been set]
 * - conditions : Normal find('list') conditions.
 * - cache : Enable cache.
 *
 * @package app
 * @subpackage app.models.behaviors
 */
class EnumerableBehavior extends ModelBehavior {

	/**
	 * Name of the behavior in CakePHP Registry.
	 * 
	 * @version 0.1
	 * @since 0.1
	 * @var string
	 * @access public
	 */
	var $name = 'EnumerableBehavior';
	
	/**
	 * Contain settings indexed by model name.
	 * 
	 * @version 0.1
	 * @since 0.1
	 * @var mixed
	 * @access public
	 */
	var $settings = array();
	
	/**
	 * Enumerations container.
	 * 
	 * @version 0.1
	 * @since 0.1
	 * @var array
	 * @access private
	 */
	var $__enum = array();

	/**
	 * Start up hooks from the model
	 * 
	 * @version 0.1
	 * @since 0.1
	 * @param object &$Model object using this behavior
	 * @param mixed $settings
	 * @return void
	 * @access public
	 * @see cake/libs/model/ModelBehavior::setup()
	 */
	function setup(&$Model, $settings) {
		if (isset($settings['fieldList']) && count($settings['fieldList']) > 2) {
			trigger_error('EnumerableBehavior doesn\'t support more than 2 fields. (too many fields in fieldList)', E_USER_WARNING);
		}
		$this->settings[$Model->alias] = array_merge(array(
			'fieldList' => array($Model->primaryKey, $Model->displayField),
			'conditions' => array(),
			'cache' => false
		), $settings);
	}

	/**
	 * Get all values.
	 * 
	 * @version 0.1
	 * @since 0.1
	 * @param object &$Model object using this behavior
	 * @param boolean $reset reset cache
	 * @return mixed associated keys
	 * @access public
	 */
	function enumAll(&$Model, $reset = false) {
		if ($this->settings[$Model->alias]['cache'] && !$reset) {
			$cached = Cache::read("{$this->name}.{$Model->alias}");
			if ($cached !== false) {
				$this->__enum[$Model->alias] = $cached;
			}
		}

		if (empty($this->__enum[$Model->alias]) || $reset) {
			$this->__enum[$Model->alias] = $Model->find('list', array(
				'conditions' => $this->settings[$Model->alias]['conditions'],
				'fields' => $this->settings[$Model->alias]['fieldList']
			));

			if ($this->settings[$Model->alias]['cache']) {
				Cache::write(
					"{$this->name}.{$Model->alias}",
					$this->__enum[$Model->alias]
				);
			}
		}

		return $this->__enum[$Model->alias];
	}

	/**
	 * Get value of the associated key.
	 * 
	 * @version 0.1
	 * @since 0.1
	 * @param object &$Model object using this behavior
	 * @param integer|mixed $value in the enumeration
	 * @param boolean reset cache
	 * @return mixed associated key
	 * @access public
	 */
	function enum(&$Model, $value, $reset = false) {
		if (
			(!isset($this->__enum[$Model->alias]) && empty($this->__enum[$Model->alias])) ||
			$reset
		) {
			$this->enumAll($Model, $reset);
		}

		if (is_int($value)) {
			return $this->__enum[$Model->alias][$value];
		}
		
		return array_search($value, $this->__enum[$Model->alias]);
	}
}