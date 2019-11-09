<?php 
class App_Model_General_DbTable_EmailTemplate extends Zend_Db_Table_Abstract
{
   // protected $_name = 'g014_emailtemplate';
    protected $_name = 'email_template_head';
	protected $_primary = "eth_id";
	protected $_subname = 'email_template_detl';
	protected $_subprimary = "etd_id";
	
	
	
	public function getData($id,$language){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
	                ->from(array('eth'=>$this->_name))
	                ->joinleft(array('etd'=>$this->_subname),'etd.etd_eth_id=eth.eth_id',array('subject'=>'etd.etd_subject','body'=>'etd.etd_body'))
	                ->where('eth.eth_id = '.$id )
	                ->where('etd.etd_language = '.$language );
	     
	   
	    $row = $db->fetchRow($select);	
		return $row;           
	}
	
	
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from($this->_name);
		
		return $select;
	}
	
	public function addData($data){
		
		$this->insert($data);
	}
	
	public function updateData($data,$id){
		
		$this->update($data, $this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary .' =' . (int)$id);
	}
}
?>