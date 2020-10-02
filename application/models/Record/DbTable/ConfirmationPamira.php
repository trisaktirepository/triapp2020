<?php
class App_Model_Record_DbTable_ConfirmationPamira extends Zend_Db_Table_Abstract {
    
	protected $_name = 'tbl_confirmasi_pamira';
	protected $_primary='id';
	
	
    public function dispatcher($registration_id,$role) {
    	$auth = Zend_Auth::getInstance();
    	if ($auth->getIdentity()->IdRole==$role && $auth->getIdentity()->registration_id==$registration_id)
    		$this->_redirect('/extra-activity/pemira');
   	}
    public function addData($bind){
        $db = Zend_Db_Table::getDefaultAdapter();
        $id=$this->insert($bind);
        return $id;
    }
    
   public function isOpen(){
   		$dbActCalend=new App_Model_General_DbTable_ActivityCalendar();
   		$row=$dbActCalend->getActiveEvent(46);
   		if ($row) return true;
   }
    	
    public function deleteData($id){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$this->delete($this->_primary."=".$id);
    }
    
    
    public function updateData($bind, $id){
        $db = Zend_Db_Table::getDefaultAdapter();
        $update = $this->update($bind, $this->_primary.'='.$id);
        return $update;
    }
    
    public function inactive( $code,$idstd){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$where="confirmation='".$code."' and idStudentRegistration=".$idstd." and active='1'";
    	$update = $this->update(array('active'=>'0'), $where);
    	return $update;
    }
    
    public function isIn($kode, $idstd){
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$select = $db->select()
    	->from(array('a'=>'tbl_confirmation'))
    	->where('a.confirmation=?',$kode)
    	->where('a.IdStudentRegistration=?',$idstd)
    	->where('a.active= "1"');
    	$row=$db->fetchRow($select);
    	//echo var_dump($row);exit;
    if ($row) return 0; 
    	else {
    		$select = $db->select()
    		->from(array('a'=>'tbl_confirmation')) 
    		->where('a.IdStudentRegistration=?',$idstd)
    		->where('a.active= "1"');
    		$row=$db->fetchRow($select);
    		$ntry=$row['n_try'];
    		$id=$row['id'];
    		if ($ntry<4)
    			$db->update('tbl_confirmation',array('n_try'=>$ntry+1),'id='.$id);
    		else 
    			$db->update('tbl_confirmation',array('active'=>"0"),'idStudentRegistration='.$idstd);
    		return $ntry+1;
    	}
    	 
    }
    
    public function genRandomNumber(){
    	return mt_rand(100000, 999999);
    }
}