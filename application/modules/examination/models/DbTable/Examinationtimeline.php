<?php
class Examination_Model_DbTable_Examinationtimeline extends Zend_Db_Table_Abstract { //Model Class for Users Details
	protected $_name = 'tbl_marksentrytimeline';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnAddtimeline($larrformData){

		$IdUniversity = $larrformData['IdUniversity'];
		$where = "IdUniversity = '".$IdUniversity."' ";
		$this->delete($where);

		$totalIdcomponent = count($larrformData['Idcomponentsgrid']);
		for($i=0;$i<$totalIdcomponent;$i++) {

			$paramArr = array(
					'IdUniversity'=>$larrformData['IdUniversity'],
					'IdComponent'=>$larrformData['Idcomponentsgrid'][$i],
					'IdComponentItem'=>$larrformData['Idcomponentsitemgrid'][$i],
					'StartDate'=>$larrformData['StartDategrid'][$i],
					'EndDate'=>$larrformData['EndDategrid'][$i],
					'UpdDate'=>$larrformData['UpdDate'],
					'UpdUser'=>$larrformData['UpdUser'],
			);
			$this->insert($paramArr);
		}

	}



	public function fngetTimelineComplete($IdUniversity) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_marksentrytimeline'), array('a.*'))
		->joinLeft(array('b' => 'tbl_examination_assessment_type'),'b.IdExaminationAssessmentType = a.IdComponent ', array('b.Description as ComponentName') )
		->joinLeft(array('c' => 'tbl_examination_assessment_item'),'c.IdExaminationAssessmentType = a.IdComponentItem ', array('c.Description as ComponentitemName') )
		->where('a.IdUniversity =?',$IdUniversity);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fngetTimeline($IdUniversity) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_marksentrytimeline'), array('a.*'))
		->where('a.IdUniversity =?',$IdUniversity);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}



}
?>