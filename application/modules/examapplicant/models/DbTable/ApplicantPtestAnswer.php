<?php

class Examapplicant_Model_DbTable_ApplicantPtestAnswer extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_ptest_ans';
	protected $_primary = "apa_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('a'=>$this->_name))
					->where('a.apa_id = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('apa'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		return $row;
		
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('a'=>$this->_name))
						->join(array('b'=>'applicant_transaction'),'b.at_trans_id = a.apa_trans_id')
						->join(array('c'=>'applicant_profile'),'c.appl_id = b.at_appl_id')
             		    //->where("a.apa_status=1")
						->order('a.'.$this->_primary.' ASC');
			  			
		return $selectData;
	}
	
	public function searchPaginate($post = array()){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('a'=>$this->_name))
						->join(array('b'=>'applicant_transaction'),'b.at_trans_id = a.apa_trans_id')
						->join(array('c'=>'applicant_profile'),'c.appl_id = b.at_appl_id')
						->where("a.apa_ptest_code LIKE '%".$post['ptcode']."%'")
						->where("a.apa_date LIKE '%".$post['date']."%'")
						->where("concat(c.appl_fname, ' ', c.appl_mname,' ', c.appl_lname) LIKE '%".$post['name']."%'")
             		    ->order('a.'.$this->_primary.' ASC');
      		echo $selectData;		
		return $selectData;
	}
	
	public function addData($postData){
		
		$auth = Zend_Auth::getInstance(); 
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$sql ="select * from applicant_ptest_ans where apa_trans_id ='".$postData['apa_trans_id']."' and pcode ='".$postData['pcode']."'";
		$row = $db->fetchRow($sql);
		 
		$sqld ="delete from applicant_ptest_ans where apa_id='".$row["apa_id"]."'";
		$db->query($sqld);
		$sqld2 ="delete from applicant_ptest_ans_detl where apad_apa_id='".$row["apa_id"]."'";
		$db->query($sqld2);
		$sqld3="delete from applicant_ptest_comp_mark where apcm_at_trans_id='".$postData['apa_trans_id']."' and pcode ='".$postData['pcode']."'";
		$db->query($sqld3);
		$db->beginTransaction();
		
		$schemaDetailDb = new Application_Model_DbTable_PlacementTestSchemaDetail();
		
		try {
			//head data
		   	$data = array(
		        'apa_trans_id' => $postData['apa_trans_id'],
		        'apa_ptest_code' => $postData['apa_ptest_code'],
				'apa_set_code' => $postData['apa_set_code'],
			   	'apa_date' => date ('Y-m-d h:i:s'),
		   		'pcode' => $postData['pcode'],
			   	'apa_user_by' => $auth->getIdentity()->iduser
				);
		
			$ok = $db->insert($this->_name,$data);
			$id = $db->lastInsertId($this->_name);
			//detail data
			$i=0;
			foreach ($postData['answer'] as $answer){
				
				//check answer
				$question_result = 0;
				if($answer!=""){
					$question_result = $schemaDetailDb->checkAnswer2($postData['apa_set_code'],($i+1),$answer);
				}
				
				$dtl_data = array(
								'apad_apa_id' => $id,
								'apad_ques_no' =>($i+1),
								'apad_appl_ans' => $answer,
								'apad_status_ans' => $question_result
							);
				
				$db->insert('applicant_ptest_ans_detl',$dtl_data);
				$i++;
			}
		    
		    $db->commit();
		
		} catch (exception $e) {
			$db->rollBack();
    		echo $e->getMessage();
		}
		return $id;
	}
	
	public function updateData($postData,$id){
		
		$auth = Zend_Auth::getInstance(); 
		
		$data = array(
		        'apa_trans_id' => $postData['apa_trans_id'],
		        'apa_ptest_code' => $postData['apa_ptest_code'],
				'apa_set_code' => $postData['apa_set_code']
				);
			
		$this->update($data, 'apa_id = '. (int)$id);
	}
	
	public function getDataByStatus($status=1){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "Select apa.*,ap.apt_ptest_code,ap.apt_id from ".$this->_name." apa 
				inner join applicant_ptest as ap
				on ap.apt_at_trans_id=apa.apa_trans_id
				where apa_status=$status		
				";
		//echo $sql;exit;
		$row = $db->fetchAll($sql);
/*		echo "<pre>";
		print_r($row);
		echo "</pre>";*/
		return $row;
	}
	
	public function getDataById($id,$aps_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "Select apa.*,ap.apt_ptest_code,ap.apt_id from ".$this->_name." apa 
				inner join applicant_ptest as ap
				on ap.apt_at_trans_id=apa.apa_trans_id
				where apa_id=$id and apt_aps_id = $aps_id		
				";
		//echo $sql;exit;
		$row = $db->fetchRow($sql);
/*		echo "<pre>";
		print_r($row);
		echo "</pre>";*/
		return $row;
	}
	public function getPaginateData2($scid,$rid,$pcode){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('a'=>$this->_name))
						->joinright(array('b'=>'applicant_transaction'),'b.at_trans_id = a.apa_trans_id')
						->joinleft(array('c'=>'applicant_profile'),'c.appl_id = b.at_appl_id')
             		    ->joinleft(array('d'=>'applicant_ptest'),'b.at_trans_id=d.apt_at_trans_id')
						->where("d.apt_aps_id= ? ",$scid)
						->where("d.apt_room_id= ? ",$rid)
						->where("a.pcode= ? ",$pcode)
						->order('a.'.$this->_primary.' ASC');
			  //echo $selectData;	
			  //echo $scid.$rid;		
		return $selectData;
	}
	
	public function checkSchema($schema){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql="SELECT *
				FROM `appl_ansscheme` where aas_set_code = '$schema'";
		//echo $sql;//exit;
		$row = $db->fetchRow($sql);
		
		if(is_array($row)) return true;
		else return false;
	}
}

