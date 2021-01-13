<?php 
class App_Model_Application_DbTable_ApplicantEducation extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_education';
	protected $_primary = 'ae_id';
	
	public function getData($appl_id, $txn_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from(array('ae'=>$this->_name))
					->joinleft(array('sd'=>'school_discipline'),'sd.smd_code=ae.ae_discipline_code',array('discipline'=>'sd.smd_desc'))
					->where("ae.ae_appl_id = '".$appl_id."'")
					->where("ae.ae_transaction_id = '".$txn_id."'");
       
        $row = $db->fetchRow($select);
        
        if($row){
        	return $row;
        }else{
        	return null;
        }
	}
	
	public function getDataSchool($appl_id, $txn_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		

		$select = $db ->select()
		->from(array('at'=>'applicant_transaction'))
		->joinLeft(array('ap'=>'applicant_ptest'),'ap.apt_at_trans_id=at.at_trans_id')
		->joinLeft(array('h'=>'appl_placement_head'),'h.aph_placement_code=ap.apt_ptest_code')
		->where('at_trans_id=?',$txn_id);
		$row=$db->fetchRow($select);
		
		if ($row['level_kkni'] > "6") {
			$select = $db ->select()
        	->from(array('ae'=>$this->_name))
        	->join(array('sm'=>'tbl_sms_pdpt'),'sm.sms_id = ae.ae_institution',array('id_sms','smd_desc'=>'CONCAT(nm_lemb," (",IFNULL(nm_jenjang,"-"),") ")'))
        	->join(array('pt'=>'tbl_pt_pdpt'),'sm.id_sp=pt.id_sp',array('sm_name'=>'nm_sp'))
        	->where("ae.ae_appl_id = '".$appl_id."'")
			->where("ae.ae_transaction_id = '".$txn_id."'");
        	$row = $db->fetchRow($select);
		} else {
		     $select = $db ->select()
						->from(array('ae'=>$this->_name))
						->joinLeft(array('sm'=>'school_master'),'sm.sm_id = ae.ae_institution')
						->joinLeft(array('sd'=>'school_discipline'),'sd.smd_code = ae.ae_discipline_code')
						->where("ae.ae_appl_id = '".$appl_id."'")
						->where("ae.ae_transaction_id = '".$txn_id."'");
	       
	        $row = $db->fetchRow($select);
		}
       return $row;
	}
	
	public function getDataByapplID($appl_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from($this->_name)
					->where("ae_appl_id = '".$appl_id."'")
					->order("ae_id desc");
       
        $row = $db->fetchRow($select);
        
        if($row){
        	return $row;
        }else{
        	return null;
        }
	}
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
public function getEducationDetail($transaction_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db ->select()
					->from(array('ae'=>$this->_name),array())
					->joinLeft(array('aed'=>'applicant_education_detl'),'aed.aed_ae_id = ae.ae_id',array('aed_sem1','aed_sem2','aed_sem3','aed_sem4','aed_sem5','aed_sem6','aed_average'))					
					->joinLeft(array('ss'=>'school_subject'),'ss.ss_id=aed.aed_subject_id ',array('subject_english'=>'ss.ss_subject','subject_bahasa'=>'ss.ss_subject_bahasa','ss_id'=>'ss.ss_id'))
					->where("ae_transaction_id = '".$transaction_id."'");
       
        $row = $db->fetchAll($select);
        return $row;
        
	}
	
	
	public function getUTBKDetail($transaction_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
	//get from UTBK
	  		$select = $db->select()
	  			->distinct()
	  			->from(array('a'=>'appl_placement_utbk_test_type'),array())
	  			->join(array('c'=>'appl_component'),'a.idcomponent=c.ac_id',array('subjectname'=>'ac_comp_name_bahasa'))
	  			->join(array('e'=>'appl_placement_detl'),'e.apd_comp_code=c.ac_comp_code')
	  			->join(array('d'=>'applicant_ptest_comp_mark'),'d.apcm_apd_id=e.apd_id')
	  			->where('d.apcm_at_trans_id=?',$transaction_id) ;
	  			 
	  		$stmt = $db->query($select);
	  		$row = $stmt->fetchAll();
	  		 
			return $row;
	
	}
	
public function getAverageMark($appl_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	    $select = $db ->select()
					->from(array('ae'=>$this->_name))
					->joinLeft(array('aed'=>'applicant_education_detl'),'aed.aed_ae_id = ae.ae_id',array('aed_average'))
					->where("ae_appl_id = '".$appl_id."'");
       
				
        $education = $db->fetchAll($select);
        
	       $total_subject=count($education);
			$total_mark=0;
			$everage=0;
			
			foreach ($education as $e){
				$total_mark = ceil($total_mark)+ ceil($e["aed_average"]);
			}
			$everage = $total_mark/$total_subject;
		
			return $everage;
	}
	
	
	
}
?>