<?php 
class App_Model_Finance_DbTable_BatchTerm extends Zend_Db_Table_Abstract
{
    protected $_name = 'f007_batchTerm';
	protected $_primary = "id";
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		if($id!=0){
	                
			$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->where('f.'.$this->_primary.' = ' .$id);	                
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetch();
	        
			if(!$row){
				throw new Exception("There is No Data");
			}
        
		}else{
			$select = $db->select()
	                ->from(array('f'=>$this->_name))
	                ->order('name ASC');
			                     
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	        
		}
		
		return $row;
	}
	
	public function listCompany($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('c'=>'g013_takafuloperator'))
	                ->joinLeft(array('b'=>'f007_batchterm'),'c.id = b.id_batch',array('idBatchTerm'=>'id'))
	                ->joinLeft(array('t'=>'f002_paymentterm'),'t.id = b.id_term',array('termName'=>'name','idPayterm'=>'id'))
	                ->order('c.name ASC')
	                ->where('c.idClienttype ='.$id);
	
		return $select;
	}
	
	
	public function getBatchTerm($idComp){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('c'=>$this->_name))
	                ->where('c.id_batch ='.$idComp);
	    $row = $db->fetchRow($select);      
	
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	                ->from(array('f'=>$this->_name));
		
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