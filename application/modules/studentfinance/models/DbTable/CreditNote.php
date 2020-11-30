<?php
class Studentfinance_Model_DbTable_CreditNote extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'credit_note';
	protected $_primary = "cn_id";
		
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('cn'=>$this->_name));
		
		if($id!=0){
			$selectData->where("cn.id = '".$id."'");
			
			$row = $db->fetchRow($selectData);
		}else{
			
			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;	
		}				
		
	}
	
	public function getPaginateData($approve=null, $cancel=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db->select()
				->from(array('cn'=>$this->_name))
				->join(array('ap'=>'applicant_profile'),'ap.appl_id = cn.appl_id', array("concat_ws(' ',ap.appl_fname,ap.appl_mname,ap.appl_lname)name"))
				->joinLeft(array('ap_c'=>'applicant_profile'),'ap_c.appl_id = cn.cn_creator', array('creator_name'=>"concat_ws(' ',ap.appl_fname,ap.appl_mname,ap.appl_lname)"));
				
				
		if($approve != null && $approve == false){
			$selectData->where("cn.cn_approver = null");
			$selectData->where("cn.cn_approve_date = null");
		}else
		if($approve != null && $approve == true){
			$selectData->where("cn.cn_approver is not null");
			$selectData->where("cn.cn_approve_date is not null");
		}else
				
		if($cancel != null && $cancel == true){
			$selectData->where("cn.cn_cancel_by is not null");
			$selectData->where("cn.cn_cancel_date is not null");
		}else				
		if($cancel != null && $cancel == false){
			$selectData->where("cn.cn_cancel_by = null");
			$selectData->where("cn.cn_cancel_date = null");
		}
			
		return $selectData;
	}
	
	
	public function insert(array $data){
		
		$auth = Zend_Auth::getInstance();
		
		if(!isset($data['cn_creator'])){
			$data['cn_creator'] = $auth->getIdentity()->appl_id;
		}
		
		$data['cn_create_date'] = date('Y-m-d H:i:s');
			
        return parent::insert($data);
	}		
		

	public function updateData(array $data,$where){
		$auth = Zend_Auth::getInstance();
		$data['update_by'] = $auth->getIdentity()->appl_id;
		$data['update_date'] = date('Y-m-d H:i:s');
		
		return parent::update($data, $where);
	}
	
	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'status' => 0				
			);
				
			$this->update($data, "id = '".$id."'");
		}
	}	
	
	public function isIn($nobill,$idstd,$desc){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('d'=>$this->_name))
		->where("d.cn_billing_no = '".$nobill."'")
		->where('d.IdStudentRegistration=?',$idstd)
		->where('d.cn_description=?',$desc);
	
		$row = $db->fetchRow($selectData);
	
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function isInBillApplId($nobill,$idstd){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('d'=>$this->_name))
		->where("d.cn_billing_no = '".$nobill."'")
		->where('d.appl_id=?',$idstd);
	
		$row = $db->fetchRow($selectData);
	
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
	
	public function getCN($nobill,$feeid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('d'=>$this->_name))
		->join(array('dd'=>'credit_note_detail'),'d.cn_id=dd.cnd_cn_id')
		->where("d.cn_billing_no = '".$nobill."'")
		->where('dd.cnd_fi_id=?',$feeid);
	
		$row = $db->fetchAll($selectData);
	
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	
	}
}

