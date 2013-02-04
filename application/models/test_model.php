<?php

class Test_model extends ExtendedModel
{

    function __construct()
    {
        parent::__construct();
    }

    public function getValue($iInput)
    {
        $oResult = $this->db->query('SELECT `value` FROM `test_table` WHERE id = ?', array($iInput));
        if ($oResult->num_rows()) {
            $sValue = $oResult->row()->value;
        } else {
            $sValue = 'NO VALUE!';
        }
        return $sValue;
    }

    /**
     * Creates a test table, or wipes an existing one if it's there and creates this one.
     * @return Test_model
     */
    public function createTable()
    {
        $this->db->query('DROP TABLE IF EXISTS `test_table`');
        $this->db->query('CREATE TABLE `test_table` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `value` varchar(15) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `value_index` (`value`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
        $this->db->query('INSERT INTO `test_table` (`value`) VALUES (\'value1\'), (\'value2\')');
        return $this;
    }


}
