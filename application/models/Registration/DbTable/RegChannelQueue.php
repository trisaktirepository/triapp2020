<?php

class  App_Model_Registration_DbTable_RegChannelQueue extends Zend_Db_Table_Abstract {

	protected $_name = 'registration_channel_queue';
	protected $_primary = "id_rcq";
	
	public function addData($data){		
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('count'=>'COUNT(*)'))
		->join(array('b'=>'registration_channel_clerk'),'a.d_rcc=b.id_rcc',array('id_rcc'))
		->where("status is null")
		->where('d.id_rcc=?',$data['id_rcc']);
		$row = $db->fetchRow($select);
		$data['queue_no']=$row['count'];
	   
		$id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	 
	
	public function getData($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where("id_rc = ?",$id);
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function getClerk(){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('count'=>'COUNT(*)'))
		->join(array('b'=>'registration_channel_clerk'),'a.d_rcc=b.id_rcc',array('id_rcc'))
		->where("status is null")
		->group('b.id_rcc')
		->order('COUNT(*) ASC');
		$row = $db->fetchRow($select);
		 
		return $row;
	}
	
	 
	public function isInQueue($idstd){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where("a.transaction_id = ?",$idstd);
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function myQueue($iduser,$intake,$date=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'registration_channel_clerk'),'a.id_rcc=b.id_rcc')
		->join(array('c'=>'applicant_transaction'),'a.transaction_id=c.at_trans_id')
		->join(array('d'=>'applicant_profile'),'c.at_appl_id=d.appl_id')
		->join(array('e'=>'reg_date_setup'),'d.rds_id=c.rsd_id')
		->where("b.iduser = ?",$iduser)
		//->where("a.queue_no>0")
		//->where('b.status is null')
		->where('e.rds_intake=?',$intake)
		->order('a.date_login asc');
		if ($date!=null) $select->where('rds_date=?',$date);
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function updateQueue($idrcc){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = 'update registration_channel_queue set queue_no=queue_no-1 where queue_no<>0 and id_rcc='.$idrcc;
		$row = $db->query($select);
		return $row;
	}
	
	 
	
}

?>