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
	
	
	public function isExamScript($trx, $testtype){
		 
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
			->from(array('a'=>$this->_name))
			->where('a.apa_trans_id = '.$trx)
			->where('a.test_type=?',$testtype);
				
			$row = $db->fetchRow($select);
		 	if ($row) {
		 		$select = $db->select()
		 		->from(array('a'=>'applicant_ptest_ans_detl'),array('count'=>'COUNT(*)'))
		 		->where('a.apad_apa_id = '.$row['apa_id']);
		 		
		 		$det = $db->fetchRow($select);
		 		$row['n_of_quest']=$det['count'];
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
		
		 
		$response=array();
		try {
			$success="1";
			//component map
			foreach ($postData['component'] as $key=>$comp) {
				$select=$db->select()
					->from(array('a'=>'tbl_exam_comp_map'))
					->where('a.ac_idori=?',$comp['ac_id']);
				$row=$db->fetchRow($select);
				$postData['component'][$key]['ac_id']=$row['ac_iddest'];
			}
			//head data
		   	$data = array(
		        'apa_trans_id' => $postData['apa_trans_id'],
		        'apa_ptest_code' => $postData['apa_ptest_code'],
				'apa_set_code' => $postData['apa_set_code'],
			   	'apa_date' => date ('Y-m-d h:i:s'),
		   		'pcode' => $postData['pcode'],
		   		'idConfig'=>$postData['config']['idConfig'],
		   		'test_type'=>$postData['test_type'],
			   	'apa_user_by' => $auth->getIdentity()->appl_id
				);
		
		   	//echo var_dump($data);
		   //	$id=1;
			$ok = $db->insert($this->_name,$data);
			$id = $db->lastInsertId($this->_name);
			
			$config=$postData['config'];
			if ($config['config_mode']==1861) {
				//random set from several selected exam set
				$select=$db->select()
					->from(array('a'=>'appl_placement_examsets'))
					->where('a.ape_aph_id=?',$postData['config']['aph_id'])
					->where('a.test_type=?',$postData['test_type']);
				$set=$db->fetchAll($select);
				if ($set) {
					// echo var_dump($set);
					//get random set according to config
					$idx=array_rand($set,1);
					$idSet=$set[$idx]['ape_idSet'];
					$i=1;
					foreach ($postData['component'] as $comp) {
						$idcomp=$comp['ac_id'];
						//get config component
						$select=$db->select()
						->from(array('a'=>'tbl_question_set_config'))
						->where('a.qsc_idSet=?',$idSet);
						$config=$db->fetchRow($select);
						
						$select=$db->select()
						->from(array('a'=>'tbl_question_bank'))
						->where('a.from_setcode=?',$idSet)
						->where('a.subject=?',$idcomp)
						->where('a.parent="0"');
						$questionset=$db->fetchAll($select);
						//echo var_dump($questionset);
						if ($questionset) {
							if ($config['qsc_suffle']=="1") {
								//suffle
								$qindex='';
								foreach ($questionset as $key=>$quest) {
									$qindex=$qindex.','.$key;	
								} 
								$qindex=explode(',', $qindex);
								unset($qindex[0]);
								echo var_dump($qindex);
								$qindex=array_rand($qindex,$config['qsc_n_question']);
								echo var_dump($qindex);exit;
								foreach ($qindex as $idx) {
									;
								}
							}
							foreach ($questionset as $quest) {
								$dtl_data = array(
											'apad_apa_id' => $id,
											'apad_ques_no' =>$i,  
											'idQuestion'=>$quest['idQuestion']
										);
								//echo var_dump($dtl_data);
								//echo '<br>';
								$i++;
							$db->insert('applicant_ptest_ans_detl',$dtl_data);
							}
						} else $success="0";
						
					}
					//exit;
					 
				} else $success="0";
			} else if ($config['config_mode']==1862) {
				//random component from several selected exam set
				 
				$select=$db->select()
				->from(array('a'=>'appl_placement_examsets'))
				->where('a.ape_aph_id=?',$postData['config']['aph_id'])
				->where('a.test_type=?',$postData['test_type']);
				$set=$db->fetchAll($select);
				if ($set) {
					foreach ($postData['component'] as $keycomp=>$comp) {
						$idcomp=$comp['ac_id'];
						$selectedSet=array_rand($set);
						echo var_dump($selectedSet);echo '<br>';
						$postData['component'][$keycomp]['idSet']=$selectedSet[0]['ape_idSet'];
					}
					
					$i=1;
					foreach ($postData['component'] as $comp) {
						$idcomp=$comp['ac_id'];
						$idSet=$comp['idSet'];
						$select=$db->select()
						->from(array('a'=>'tbl_question_bank'))
						->where('a.from_setcode=?',$idSet)
						->where('a.subject=?',$idcomp);
						$questionset=$db->fecthAll($select);
						if ($questionset) {
							foreach ($questionset as $quest) {
								$dtl_data = array(
										'apad_apa_id' => $id,
										'apad_ques_no' =>$i,
										'idQuestion'=>$quest['idQuestion']
								);
								$i++;
								$db->insert('applicant_ptest_ans_detl',$dtl_data);
							}
						} else $success="0";
						 
					}
					
					
				}
			} else if ($config['config_mode']==1863) {
				//random question direct from question bank
				$selectset=$db->select()
				->from(array('a'=>'appl_placement_examsets'),array('ape_idSet'))
				->where('a.ape_aph_id=?',$postData['config']['aph_id'])
				->where('a.test_type=?',$postData['test_type']);
				$set=$db->fetchAll($selectset);
				if ($set) {
					foreach ($postData['component'] as $keycomp=>$comp) {
						$idcomp=$comp['ac_id'];
						$select=$db->select()
						->from(array('a'=>'tbl_question_bank'))
						->where('a.from_setcode in ('.$selectedSet.')')
						->where('a.subject=?',$idcomp);
					}
						
					$i=1;
					foreach ($postData['component'] as $comp) {
						$idcomp=$comp['ac_id'];
						$idSet=$comp['idSet'];
						
						$questionset=$db->fecthAll($select);
						if ($questionset) {
							foreach ($questionset as $quest) {
								$dtl_data = array(
										'apad_apa_id' => $id,
										'apad_ques_no' =>$i,
										'idQuestion'=>$quest['idQuestion']
								);
								$i++;
								$db->insert('applicant_ptest_ans_detl',$dtl_data);
							}
						} else $success="0";
						 
					}
						
						
				}
			} 
			
		  	if ( $success=="1")  {
		    	$db->commit();
		    	$response=array('apa_id'=>$id,'n_of_quest'=>$i-1);
		  	} else {
		  		$db->rollBack();
		  		$response=array();
		  	}
		 } catch (exception $e) {
			$db->rollBack();
    		echo $e->getMessage();
		} 
		return $response;
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

