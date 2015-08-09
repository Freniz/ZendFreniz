<?php
require_once 'Zend/Search/Lucene/Document.php';
class Search_Document extends Zend_Search_Lucene_Document
{
	public function getField($fieldName){
		if(!array_key_exists($fieldName, $this->_fields)){
			return false;
		}
		return $this->_fields[$fieldName];
	}
}