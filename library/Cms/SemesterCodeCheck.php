<?php
class Cms_SemesterCodeCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Semester Code already exist'
    );

    public function isValid($value, $context = null) {
        $value = (string) $value;
        $validator = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => 'tbl_semestermaster',
                            'field' => 'SemesterMainCode'
                        )
        );
        if ($validator->isValid($value)) {
            $this->_error(self::NOT_MATCH);
            return true;
        } else {
            return false;
        }
    }

}

?>
