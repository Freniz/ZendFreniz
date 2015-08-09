<?php

require_once ('Zend/Auth/Adapter/DbTable.php');
class MyAuth_Adapter extends Zend_Auth_Adapter_DbTable {
	protected function _authenticateCreateSelect(){
		if (empty($this->_credentialTreatment) || (strpos($this->_credentialTreatment, '?') === false)) {
			$this->_credentialTreatment = '?';
		}
		
		$credentialExpression = new Zend_Db_Expr(
				'(CASE WHEN ' .
				$this->_zendDb->quoteInto(
						$this->_zendDb->quoteIdentifier($this->_credentialColumn, true)
						. ' = ' . $this->_credentialTreatment, $this->_credential
				)
				. ' THEN 1 ELSE 0 END) AS '
				. $this->_zendDb->quoteIdentifier(
						$this->_zendDb->foldCase('zend_auth_credential_match')
				)
		);
		
		// get select
		$dbSelect = clone $this->getDbSelect();
		$dbSelect->from($this->_tableName, array('*', $credentialExpression))
		->where($this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ? or email=?', $this->_identity);
		
		return $dbSelect;
	}
}

?>