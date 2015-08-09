<?php

/**
 * places
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_places extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'places';
	protected $_indexPath='../application/chk';

	protected $authIdentity;
	public function __construct($db){
	$this->_db=$db;
	}
	public function getPlacesInfo($placeid)
	{
		$select=$this->_db->select()->from($this->_name)->joinRight('placesinfo', 'places.infoid=placesinfo.id')->limit(100);
			return $this->_db->fetchAssoc($select);
	}
	
	public function getProfile($placeid){
		$sql=$this->_db->select()->from($this->_name)->joinLeft('placesinfo', 'placesinfo.id=places.infoid',array('country_code'=>'country','province_code'=>'province'))->joinLeft('country','country.code=placesinfo.country','name as country_name')->joinLeft('image','image.imageid=places.placepic','url as placepic_url')->where('places.id=?',$placeid);
		$result=$this->_db->fetchRow($sql);
		$sql=$this->_db->select()->from('province','provincename')->where('provinceid=?',$result['country_code'].'.'.$result['province_code']);
		$result1=$this->_db->fetchRow($sql);
		$result['province_name']=$result1['provincename'];
		return $result;
	}
	
	
	public function buildplaces(){
		
		ini_set('memory_limit', '1000M');

		set_time_limit(0);
		$time=time();

		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());

		/**

		 * Create index

		 */

		$index = Zend_Search_Lucene::create($this->_indexPath);

		/**

		 * Get all users

		 */

		$sql=$this->_db->select()->from($this->_name,array('id','name','placepic'))->limit(7500);
		$result=$this->_db->fetchAssoc($sql);
		

		foreach ($result as $values){

			$doc = new Zend_Search_Lucene_Document();

			$doc->addField(

					Zend_Search_Lucene_Field::keyword('placeid', $values['id']) );

			$doc->addField(

					Zend_Search_Lucene_Field::text('placename', $values['name']) );

			$doc->addField(

					Zend_Search_Lucene_Field::unStored('placepic', $values['placepic']) );

				

			$index->addDocument($doc);

		}

		

		

		

		$index->commit();
		$elapsed=time()-$time;
		print_r($elapsed);
	}
}

