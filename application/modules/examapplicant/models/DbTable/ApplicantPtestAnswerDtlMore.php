<?php

class Examapplicant_Model_DbTable_ApplicantPtestAnswerDtlMore extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_ptest_ans_detl_more';
	protected $_primary = "apadm_id";
	public function addData($data){
		$id = $this->insert($data);
		return $id;
	}
	
	public function updateData($data,$id){
		$this->_db->update($this->_name,$data,'id = ' . (int)$id);
	}
	
	public function deleteData($id){
		$this->_db->delete($this->_name,$this->_primary . ' = ' . (int)$id);
	}
	public function getData($id=0){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->join(array('b'=>'applicant_ptest_ans_detl'),'a.apadm_apad_id=b.apad_id')
					->where('a.apadm_apad_id = '.$id);
							
			$row = $db->fetchRow($select);
		 
		return $row;
		
	}
	
	public function isIn($apadid,$aufid){
			
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'applicant_ptest_ans_detl'),'a.apadm_apad_id=b.apad_id')
		->where('a.apadm_apad_id = '.$apadid)
		->where('a.apadm_auf_id=?',$aufid);
			
		$row = $db->fetchRow($select);
			
		return $row;
	
	}
	
	public function getDataByHead($id=0){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->where('a.apadm_apad_id  = '.$id)
					->where('a.apadm_auf_id is not null');
			//echo $select;				
			$row = $db->fetchAll($select);
	 
		return $row;
		
	}	
	
	public function getDataFileByHead($id=0){
			
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'appl_upload_file'),'a.apadm_auf_id=b.auf_id')
		->where('a.apadm_apad_id  = '.$id)
		;
		//echo $select;
		$row = $db->fetchAll($select);
	
		return $row;
	
	}
	
	public function getDataTextByHead($id=0){
			
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('a'=>$this->_name)) 
		->where('a.apadm_apad_id  = '.$id)
		->where('a.apadm_text is not null'); 
		$row = $db->fetchRow($select);
	
		return $row;
	
	}
	
	public function getEvidenceByHead($id=0){
		$id = (int)$id;
	
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('a'=>$this->_name),array())
			->join(array('b'=>'appl_upload_file'),'b.auf_id=a.apad_auf_id')
			->where('a.apad_apa_id  = '.$id);
			//echo $select;
			$row = $db->fetchAll($select);
		}
		return $row;
	
	}
	
	public function getQuestionBySequence($apaid,$seqno){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('a'=>$this->_name),array('apad_id','apad_apa_id','apad_ques_no','apad_appl_ans','idQuestion'))
			->join(array('b'=>'tbl_question_bank'),'a.idQuestion=b.idQuestion',array('question_url','question_parent'))
			->where('a.apad_apa_id  = '.$apaid)
			->where('a.apad_ques_no  = '.$seqno);
			//echo $select;
			$row = $db->fetchRow($select);
			 
	 		if ($row['question_parent']!="0") {
	 			$select = $db->select()
	 			->from(array('a'=>'tbl_question_bank'))
	 			->where('a.idQuestion  = '.$row['question_parent']);
	 			$parent = $db->fetchRow($select);
	 			//echo var_dump($parent);
	 			$row['question_parent_url']=$parent['question_url'];
	 		} else $row['question_parent_url']='';
		return $row;
	
	}
	
	 
	
	 
	 

}

