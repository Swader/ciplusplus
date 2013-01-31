<?php

class Test_model extends ExtendedModel {

	function __construct()
    {
        parent::__construct();
    }

	public function getValue($iInput) {
		$oResult = $this->db->query('SELECT `value` FROM `fortrabbit` WHERE id = ?', array($iInput));
		if ($oResult->num_rows()) {
			$sValue = $oResult->row()->value;
		} else {
			$sValue = 'NO VALUE!';
		}
		return $sValue;
	}

}