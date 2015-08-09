<?php
require_once 'MySearch/MyLucene.php';
class Search1Controller extends Zend_Controller_Action
{
	protected $registry;

	protected $authIdentity;

	protected $_userindexPath='../application/usersearch/users/';
	protected $_forumindexPath='../application/search/forum/';
  protected $_placeindexPath='../application/placesearch/places/';
    public function init()
    {
        /* Initialize action controller here */
   	$this->auth=Zend_Auth::getInstance();

		if($this->auth->hasIdentity()){

			$this->authIdentity=$this->auth->getIdentity();

		}

		$this->registry=Zend_Registry::getInstance();
    }
    public function indexAction(){
    if($this->auth->hasIdentity()){
			// Streams logic has to be here
			$this->view->userid=$this->authidentity->password;
			$this->view->onlineusers=$this->authidentity;
		}
		else {
			$this->_redirect('login');
		}
    }

  public function searchAction()
    {
    	$post=$this->getRequest()->getParams();
    	//$this->view->result=$post;
    		/**
    		 * Open index
    		*/
    		$index = Search_MyLucene::open($this->_userindexPath);
    	//$term=new Zend_search_lucene_i
    		Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);
    		//$query = 'name:' . $post['name'];
    		
    		$names=trim($post['name']);
    		//foreach ($names as $name)
    		$query='username:'.$names.'*';
    		if(isset($post['skills'])){

    			$query.='&&skills:'.$post['skills'];

    		}
    		if(isset($post['school'])){
    			$query.='+school:'.$post['school'];
    		}
    		if(isset($post['college'])){
    			$query.='+college:'.$post['college'];
    		}
    		if(isset($post['ccity'])){
    			$query.='+livingin:'.$post['ccity'];
    		}
    		if(isset($post['htown'])){
    			$query.='+hometown:'.$post['htown'];
    		}
    		if(isset($post['category'])){

    			$query.='&&category:'.$post['category'];

    		}
    		if(isset($post['subcategory'])){

    			$query.='&&subcategory:'.$post['subcategory'];

    		}
    		//echo $query;
    		$result = $index->find($query);
    		/*$getdet=new Application_Model_Users($this->registry['DB']);
    		$ids=array();
    		foreach ($result as $re){
    			array_push($ids,$re->userid);
    		}
    		$this->view->results=$getdet->getminiprofile($ids); */
    		$this->view->results=$result;

    		
    }
     public function placesearchAction()
    {
    	$post=$this->getRequest()->getParams();
    	//$this->view->result=$post;
    		/**
    		 * Open index
    		*/
    		$index = Search_MyLucene::open($this->_placeindexPath);
    	//$term=new Zend_search_lucene_i
    		Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);
    		//$query = 'name:' . $post['name'];
    		
    		$names=$post['name'];
    		//foreach ($names as $name)
    		$query='placename:'.$names.'*';
    		
    		//echo $query;
    		$result = $index->find($query);
    	$this->view->results=$result;

    		
    }
    
    public function forumsearchAction()

    {

    	$search=$this->getRequest()->getParam('s');

    	$tags=$this->getRequest()->getParam('tags');

    	$order=$this->getRequest()->getParam('order');

    	//$this->view->result=$post;

    	/**

    	 * Open index

    	 */

    	$index = Search_MyLucene::open($this->_forumindexPath);

    	//$term=new Zend_search_lucene_i

    	Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);

    	//$query = 'name:' . $post['name'];

    	if($search){

    		$query='questions:'.$search.'*';

    	}

    	else $query='tags:'.$tags.'*';

    	$results = $index->find($query);

    	$questionids=array();

    	foreach ($results as $values){

    		array_push($questionids, $values->questionid);

    	}

    	$getquestion=new Application_Model_forum($this->registry['DB']);

    

    	$this->view->result=$getquestion->getTopics($questionids, $order);

    

    }
    

    public function gettagsAction(){

    	$tags=$this->getRequest()->getParam('tag');

    	//$this->view->result=$post;

    	/**

    	 * Open index

    	 */

    	$index = Search_MyLucene::open($this->_forumindexPath);

    	//$term=new Zend_search_lucene_i

    	Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);

    	//$query = 'name:' . $post['name'];

    	$query='tags:'.$tags.'*';

    	$results = $index->find($query);

    	$matches=array();

    	foreach ($results as $values){

    		$string=explode(' ', $values->tags);

    		foreach ($string as $str){

    			if(stripos($str, $tags)===0)

    				array_push($matches,strtolower($str));

    		}

    	}

    	$matches=array_unique($matches);

    	$gettags=new Application_Model_forum($this->registry['DB']);

    	$this->view->result=$gettags->gettags($matches);

    }

    public function getskillsAction(){

    	$skills=$this->getRequest()->getParam('skills');

    	//$this->view->result=$post;

    	/**

    	 * Open index

    	 */

    	$index = Search_MyLucene::open($this->_userindexPath);

    	//$term=new Zend_search_lucene_i

    	Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);

    	//$query = 'name:' . $post['name'];

    	$query='skills:'.$skills.'*';

    	$results = $index->find($query);
    	$matches=array();

    	foreach ($results as $values){

    		$string=explode(' ', $values->skills);

    		foreach ($string as $str){

    			if(stripos($str, $skills)===0)

    				array_push($matches,strtolower($str));

    		}

    	}

    	$matches=array_unique($matches);

    	$this->view->result=$matches;

    }

    
}

