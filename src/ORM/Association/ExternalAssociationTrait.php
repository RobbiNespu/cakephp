<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Cake\ORM\Association;

use Cake\ORM\Association\SelectableAssociationTrait;
use Cake\ORM\Query;
use Cake\Utility\Inflector;

/**
 * Represents a type of association that that needs to be recovered by performing
 * a extra query.
 */
trait ExternalAssociationTrait {

	use SelectableAssociationTrait {
		_defaultOptions as private _selectableOptions;
	}

/**
 * Order in which target records should be returned
 *
 * @var mixed
 */
	protected $_sort;

/**
 * Whether this association can be expressed directly in a query join
 *
 * @param array $options custom options key that could alter the return value
 * @return boolean if the 'matching' key in $option is true then this function
 * will return true, false otherwise
 */
	public function canBeJoined($options = []) {
		return !empty($options['matching']);
	}

/**
 * Sets the name of the field representing the foreign key to the source table.
 * If no parameters are passed current field is returned
 *
 * @param string $key the key to be used to link both tables together
 * @return string
 */
	public function foreignKey($key = null) {
		if ($key === null) {
			if ($this->_foreignKey === null) {
				$key = Inflector::singularize($this->source()->alias());
				$this->_foreignKey = Inflector::underscore($key) . '_id';
			}
			return $this->_foreignKey;
		}
		return parent::foreignKey($key);
	}

/**
 * Sets the sort order in which target records should be returned.
 * If no arguments are passed the currently configured value is returned
 * @param mixed $sort A find() compatible order clause
 * @return mixed
 */
	public function sort($sort = null) {
		if ($sort !== null) {
			$this->_sort = $sort;
		}
		return $this->_sort;
	}

/**
 * Returns the default options to use for the eagerLoader
 *
 * @return array
 */
	protected function _defaultOptions() {
		return $this->_selectableOptions() + [
			'sort' => $this->sort()
		];
	}

/**
 * {@inheritdoc}
 *
 */
	protected function _buildResultMap($fetchQuery, $options) {
		$resultMap = [];
		$key = (array)$options['foreignKey'];

		foreach ($fetchQuery->all() as $result) {
			$values = [];
			foreach ($key as $k) {
				$values[] = $result[$k];
			}
			$resultMap[implode(';', $values)][] = $result;
		}
		return $resultMap;
	}

/**
 * Parse extra options passed in the constructor.
 *
 * @param array $opts original list of options passed in constructor
 * @return void
 */
	protected function _options(array $opts) {
		if (isset($opts['sort'])) {
			$this->sort($opts['sort']);
		}
	}

}