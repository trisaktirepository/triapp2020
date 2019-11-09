<?php 

class App_Model_Application_DbTable_Pt extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_pt_pdpt';
	protected $_primary = "id_sp";
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		// echo var_dump($data);echo $id;exit;
		 $this->update($data, $this->_primary .' = "'. $id.'"');
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function isIn($idpt) {
		 
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.id_sp=?',$idpt) ;
		return $db->fetchRow($select);
		
	}
	
	public function getByPembina($idpembina=0) {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->order('a.nm_sp');
		if ($idpembina!=0)
			$select->where('a.id_pembina=?',$idpembina) ;
		return $db->fetchAll($select);
	
	}
	
	public function getByNama($nama) {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('key'=>'id_sp','value'=>'nm_sp'))
		->order('a.nm_sp');
		 
		$select->where("a.nm_sp like '%".$nama."%'") ;
		return $db->fetchAll($select);
	
	}
	
	public function getByWilayah($idwilayah='') {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		 
		->order('a.nm_sp');
		if ($idwilayah!='') {
			$select->where('TRIM(a.id_wilayah)=?',$idwilayah) ;
			$select->ORwhere('a.id_wilayah is null') ;
		}
		//echo $select;
		return $db->fetchAll($select);
	
	}
	  
	
}
?>