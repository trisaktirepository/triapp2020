<?php

class Examapplicant_Model_DbTable_ApplicantPtestAnswerDtl extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_ptest_ans_detl';
	protected $_primary = "apad_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->where('a.apad_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('apa'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
	
	public function getDataByHead($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->where('a.apad_apa_id  = '.$id);
			//echo $select;				
			$row = $db->fetchAll($select);
		}
		return $row;
		
	}	
	
	public function getQuestionBySequence($apaid,$seqno){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('a'=>$this->_name))
			->join(array('b'=>'tbl_question_bank'),'a.idQuestion=b.idQuestion',array('question_url','question_parent'))
			->where('a.apad_apa_id  = '.$apaid)
			->where('a.apad_ques_no  = '.$seqno);
			//echo $select;
			$row = $db->fetchRow($select);
	 		if ($row['question_parent']>0) {
	 			$select = $db->select()
	 			->from(array('a'=>$this->_name))
	 			->join(array('b'=>'tbl_question_bank'),'a.idQuestion=b.idQuestion')
	 			->where('b.idQuestion  = '.$row['question_parent']);
	 			$parent = $db->fetchRow($select);
	 			$row['question_parent_url']=$parent['question_url'];
	 		} else $row['question_parent_url']='';
		return $row;
	
	}
	
	public function getMarkByCom($headId,$qfrom,$qend,$total,$testtype=0){

		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = "SELECT sum(apad_status_ans) as mark from ".$this->_name."
					Where apad_apa_id  = $headId AND  
					apad_ques_no >= $qfrom AND apad_ques_no <= $qend
				";			
		$row = $db->fetchrow($select);
		
		if($testtype==0){
			$markpercentage = $row["mark"] / $total * 100;
		}elseif($testtype==1){
			$markpercentage = $row["mark"];
		}
		//echo $select;exit;
		return $markpercentage;	
	}
	
	public function getConversion($mark,$aph_id,$ac_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($mark==""){
			$mark=0;
		}
		$select = $db->select()
					->from(array("a"=>"tpa_konversi_detl"),array("konversi"))
					->join (array("c"=>"tpa_component_test"),"c.tp_id=a.tk_id",array())
					->where ("c.ac_id = ?",$ac_id)
					->where ("c.aph_id = ?",$aph_id)
					->where ("a.mark = ?", $mark);
		$row = $db->fetchRow($select);
		//echo $select;
		if(is_array($row)){
			return $row["konversi"];
		}else{
			return 0;
		}
	}

}

