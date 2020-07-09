<?php 

class App_Model_Registration_DbTable_CoursePackage extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_register_package_scheme';
	protected $_primary = "id_crps";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		// echo var_dump($data);echo $id;exit;
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function deleteByData($idpackage,$idsemester,$idintake,$idsubject){
		$this->delete('idpackage='.$idpackage.' and idsemester='.$idsemester.' and idintake='.$idintake.' and idsubject='.$idsubject);
	}
	
	public function isIn($idpackage,$idntake,$idsemester,$idsubeject){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql = $db->select()->from($this->_name)
		->where("idpackage = ".(int)$idpackage)
		->where('idintake=?',$idntake)
		->where('idsubject=?',$idsubeject)
		->where('idsemester=?',$idsemester);
	
		return $db->fetchRow($sql);
	
	}
	 
	
}