<?php

/**
 * Comment
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
class CommentController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	protected $authIdentity;
		
	public function init()
    {
        /* Initialize action controller here */
    	
    	$this->auth=Zend_Auth::getInstance();
    	if($this->auth->hasIdentity()){
    		$this->authIdentity=$this->auth->getIdentity();
    	}
    	
    }
    public function getcommentsAction(){
    	//$this->_helper->viewRenderer->setNoRender();
    	//if(isset($this->authIdentity)){
	    	$request=$this->getRequest()->getParams();
	    	$statureids=array(0);$scribbleids=array(0);$imageids=array(0);$videoids=array(0);
	    	if(!empty($request['statures']))
	    	$statureids=explode(',', $request['statures']);
	    	if(!empty($request['scribbles']))
	    	$scribbleids=explode(',',$request['scribbles']);
	    	if(!empty($request['images']))
	    	$imageids=explode(',',$request['images']);
	    	if(!empty($request['videos']))
	    	$videoids=explode(',', $request['videos']);
	    	if(!empty($request['maxcomment'])){
	    	$commentActivity=new Application_Model_CommentActivity();
	    	$this->view->results=$commentActivity->getNewComments($request['maxcomment'],$statureids, $scribbleids, $imageids, $videoids);
	    	
	    	}
    	//}
    }
}
