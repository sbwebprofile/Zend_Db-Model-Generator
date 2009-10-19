<?="<?php\n"?>
<? if ($this->_addRequire): ?>
require_once('Zend<?=DIRECTORY_SEPARATOR?>Db<?=DIRECTORY_SEPARATOR?>Table<?=DIRECTORY_SEPARATOR?>Abstract.php');
<? endif; ?>

/**
 * Add your description here
 * 
 * @author <?=$this->_author."\n"?>
 * @copyright <?=$this->_copyright."\n"?>
 * @license <?=$this->_license."\n"?>
 */

class <?=$this->_namespace?>_Model_DbTable_<?=$this->_className?> extends Zend_Db_Table_Abstract
{
        /**
         * $_name - name of database table
         *
         * @var string
         */
	protected $_name='<?=$this->_tbname?>';

        /**
         * $_id - this is the primary key of <?=$this->_tbname?> table
         *        <?=$this->_primaryKey['type']?>
         
         *
         * @var <?=$this->_primaryKey['phptype']?>
         
         */
	protected $_id='<?=$this->_primaryKey['field']?>';

        /**
         * returns the primary key column name
         *
         * @var string 
         */
        public function getPrimaryKeyName() {
            return $this->_id;
        }

    /**
     * returns the number of rows in the table
     * @var int
     */
        public function countAllRows() {
            $query = $this->select()->from($this->_name, 'count(*) as all_count');
            $numRows = $this->fetchRow($query);
            return $numRows['all_count'];
        }
    
        public function countByQuery($where='') {

            if ($where)
                $where='where '.$where;

            $query = <<<SQL
                select count(*) as all_count from {$this->_name} $where
SQL;
            $row=$this->getAdapter()->query($query)->fetch();

            return $row['all_count'];
        }
}


