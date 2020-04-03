
<?php 

class App_Model_General_DbTable_TmpTxt extends Zend_Db_Table {
	
	protected $_name = 'tmp_txt';
	protected $_primary = "id";
	
	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	public function add($data) {
		$this->lobjDbAdpt->insert($this->_name,$data);
	}
}