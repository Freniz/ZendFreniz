<?php

class SettingsController extends Zend_Controller_Action
{

protected $authIdentity=null;
	protected $registry;
    public function init()
    {
        /* Initialize action controller here */
    	$this->request=$this->getRequest();
    	$this->auth=Zend_Auth::getInstance();
    	if($this->auth->hasIdentity()){
    	$this->authIdentity=$this->auth->getIdentity();
    	}
    	$this->registry=Zend_Registry::getInstance();
    	
    }

    public function indexAction()
    {
        // action body
        if($this->auth->hasIdentity()){
        	// Streams logic has to be here
        	$this->view->userid=$this->authIdentity->password;
        	$this->view->onlineusers=$this->authIdentity;
        }
        else {
        	$this->_redirect('login');
        }
    }
	public function accountsettingsAction(){
		if($this->auth->hasIdentity()){
			$this->view->results=$this->authIdentity;
			
			
		}
       
	}
	public function updatebasicinfoAction(){
		$this->_helper->viewRenderer->setNoRender(true);

		
		if($this->auth->hasIdentity()){

		$updatebasic=new Application_Model_UserInfo($this->registry['DB']);
		$fname=$this->getRequest()->getParam('fname');
		$lname=$this->getRequest()->getParam('lname');
		$bdy=$this->getRequest()->getParam('bdy');
		$bdm=$this->getRequest()->getParam('bdm');
		$bdd=$this->getRequest()->getParam('bdd');
		$sex=$this->getRequest()->getParam('sex');
		$religious=$this->getRequest()->getParam('religion');
		$rstatus=$this->getRequest()->getParam('rstatus');
		$skills=$this->getRequest()->getParam('skills');
		if($updatebasic->updateBasicInfo($fname, $lname, $bdy, $bdm, $bdd, $sex, $religious, $rstatus, $skills))
			echo json_encode(array('status'=>'success'));
		else echo json_encode(array('status'=>'error'));
		
		}
	}
	public function updatepersonalinfoAction(){
		$this->_helper->viewRenderer->setNoRender(true);

		

		if($this->auth->hasIdentity()){

		$updatepersonal=new Application_Model_UserInfo($this->registry['DB']);
		$body=$this->getRequest()->getParam('body');

		$look=$this->getRequest()->getParam('look');

		$smoke=$this->getRequest()->getParam('smoke');

		$drink=$this->getRequest()->getParam('drink');

		$pets=$this->getRequest()->getParam('pets');

		$passion=$this->getRequest()->getParam('passion');

		$religious=$this->getRequest()->getParam('religious');

		$ethnicity=$this->getRequest()->getParam('ethnicity');

		$humor=$this->getRequest()->getParam('humor');

		$sexual=$this->getRequest()->getParam('sexual');
		if($updatepersonal->updatepersonalInfo($body, $look, $smoke, $drink, $pets, $passion, $ethnicity, $humor, $sexual))
			echo json_encode(array('status'=>'success'));
		else echo json_encode(array('status'=>'error'));
		
		}
	}
	public function updatetofavoritesAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->auth->hasIdentity()){

		$updateschool=new Application_Model_UserInfo($this->registry['DB']);
		$pageid=$this->getRequest()->getParam('pageids');

		$category=$this->getRequest()->getParam('category');
		$type=$this->getRequest()->getParam('type');
		if($category=='college' || $category=='school' || $category=='employer'){
			$from=$this->getRequest()->getParam('from');
			$end=$this->getRequest()->getParam('end');
		 	$pageids[$pageid]=$from.','.$end;
		}
		else
		{
			$pageids=explode(',',$pageid);
		}
		if($updateschool->UpdateToFavorites($pageids, $category, $type))
			echo json_encode(array('status'=>'success'));
			else echo json_encode(array('status'=>'error'));
		}
	}
	public function updatecityAction(){
		$this->_helper->viewRenderer->setNoRender();
		if(isset($this->authIdentity)){
			$type=$this->getRequest()->getParam('type');
			$city=$this->getRequest()->getParam('city');
			if($type=='currentcity' || $type=='hometown'){
			$userinfo=new Application_Model_UserInfo($this->registry->DB);
			if($userinfo->updatecity($city,$type))
				echo json_encode(array('status'=>'success'));
				else echo json_encode(array('status'=>'error'));
			}
		}
	}
	public function getfavAction(){
		if(isset($this->authIdentity)){
			$category=$this->getRequest()->getParam('category');
			if(!empty($this->authIdentity->$category)){
				$years=array();
				
				if(in_array($category, $this->registry->pageCategories)){
					$pageids=array_keys($this->authIdentity->$category);
				foreach($this->authIdentity->$category as $pageid=>$year){
				$year=explode(',', $year);
				$years[$pageid]['from']=$year[0];
				$years[$pageid]['end']=$year[1];
				}	
				}
				else
					$pageids=$this->authIdentity->$category;
			$userinfo=new Application_Model_UserInfo($this->registry->DB);
			$this->view->results=$userinfo->getFavourites($pageids);
			$this->view->years=$years;
			}
		}
	}
	
	public function updatemoodAction(){
		$this->_helper->viewRenderer->setNoRender();
		if(isset($this->authIdentity)){
			$changemood=new Application_Model_UserInfo($this->registry['DB']);
			$mood=$this->getRequest()->getParam('mood');
			$description=$this->getRequest()->getParam('description');
			$results=$changemood->updateMood($mood, $description);
			echo json_encode($results);
		}
		else echo json_encode(array('status'=>'error'));
	}
	
	
	public function updateprivacyAction(){
		$this->_helper->viewRenderer->setNoRender();
		$request=$this->getRequest()->getParams();
		if(isset($this->authIdentity) && !empty($request['types'])){
			$a=serialize(array());
			$parametertypes=array('scribbles','statures','admires','videos','images','messages');
			if($request['types']!='all'){
				$privacy_parameters=array_intersect($parametertypes, explode(',',$request['types']));
			}
			$privacys['scribbles']=array('postspeci'=>$a,'posthidden'=>$a,'postspecificpeople'=>$a,'postignore'=>$a);
			$privacys['statures']=array('staturespeci'=>$a,'staturehidden'=>$a,'staturespecificpeople'=>$a,'statureignore'=>$a);
			$privacys['admires']=array('testyspeci'=>$a,'testyhidden'=>$a,'testyspecificpeople'=>$a,'testyignore'=>$a);
			$privacys['videos']=array('videospeci'=>$a,'videohidden'=>$a,'videospecificpeople'=>$a,'videoignore'=>$a);
			$privacys['images']=array('albumspeci'=>$a,'albumhidden'=>$a,'albumspecificpeople'=>$a,'albumignore'=>$a);
			$privacys['messages']=array('messagespecificpeople'=>$a,'messageignore'=>$a);
			
			$privacy_types=array('public','friends','private','fof','specific');
			
			$privacy=array();
			foreach ($privacy_parameters as $parameter){
				$privacy=array_merge($privacy,$privacys[$parameter]);
			}
			
			if(in_array('scribbles', $privacy_parameters)){
			if(isset($request['scribblesview']) && in_array($request['scribblesview'], $privacy_types)){
				switch($request['scribblesview']){
					case 'specific':
						unset($request['scribblesviewhidden']);
						break;
					case 'private':
						unset($request['scribblesviewhidden']);
						unset($request['scribblesviewspecific']);
						break;
					default:
						unset($request['scribblesviewspecific']);
				}
				$privacy['postvisi']=$request['scribblesview'];
			}
			if(!empty($request['scribblesviewspecific'])){
				$privacy['postspeci']=serialize(explode(',',$request['scribblesviewspecific']));
			}
			if(!empty($request['scribblesviewhidden'])){
				$privacy['posthidden']=serialize(explode(',', $request['scribblesviewhidden']));
			}
			if(!empty($request['scribblescomment']) && in_array($request['scribblescomment'], $privacy_types)){
				switch($request['scribblescomment']){
					case 'specific':
						unset($request['scribblescommentignore']);
						break;
					case 'private':
						unset($request['scribblescommentignore']);
						unset($request['scribblescommentspecific']);
						break;
					default:
						unset($request['scribblescommentspecific']);
				}
				$privacy['post']=$request['scribblescomment'];
			}
			
			if(!empty($request['scribblescommentspecific'])){
				$privacy['postspecificpeople']=serialize(explode(',',$request['scribblescommentspecific']));
			}
			
			if(!empty($request['scribblescommentignore'])){
				$privacy['postignore']=serialize(explode(',',$request['scribblescommentignore']));
			}
			}
			
			if(in_array('statures', $privacy_parameters)){
			if(isset($request['statureview']) && in_array($request['statureview'], $privacy_types)){
				switch($request['statureview']){
					case 'specific':
						unset($request['statureviewhidden']);
						break;
					case 'private':
						unset($request['statureviewhidden']);
						unset($request['statureviewspecific']);
						break;
					default:
						unset($request['statureviewspecific']);
				}
				$privacy['staturevisi']=$request['statureview'];
			}
			if(!empty($request['statureviewspecific'])){
				$privacy['staturespeci']=serialize(explode(',',$request['statureviewspecific']));
			}
			if(!empty($request['statureviewhidden'])){
				$privacy['staturehidden']=serialize(explode(',', $request['statureviewhidden']));
			}
			if(!empty($request['staturecomment']) && in_array($request['staturecomment'], $privacy_types)){
				switch($request['staturecomment']){
					case 'specific':
						unset($request['staturecommentignore']);
						break;
					case 'private':
						unset($request['staturecommentignore']);
						unset($request['staturecommentspecific']);
						break;
					default:
						unset($request['staturecommentspecific']);
				}
				$privacy['stature']=$request['staturecomment'];
			}
			if(!empty($request['staturecommentspecific'])){
				$privacy['staturespecificpeople']=serialize(explode(',',$request['staturecommentspecific']));
			}
			if(!empty($request['staturecommentignore'])){
				$privacy['statureignore']=serialize(explode(',',$request['staturecommentignore']));
			}
			}
			
			if(in_array('admires', $privacy_parameters)){
			if(isset($request['admireview']) && in_array($request['admireview'], $privacy_types)){
				switch($request['admireview']){
					case 'specific':
						unset($request['admireviewhidden']);
						break;
					case 'private':
						unset($request['admireviewhidden']);
						unset($request['admireviewspecific']);
						break;
					default:
						unset($request['admireviewspecific']);
				}
				$privacy['testyvisi']=$request['admireview'];
			}
			if(!empty($request['admireviewspecific'])){
				$privacy['testyspeci']=serialize(explode(',',$request['admireviewspecific']));
			}
			if(!empty($request['admireviewhidden'])){
				$privacy['testyhidden']=serialize(explode(',', $request['admireviewhidden']));
			}
			if(!empty($request['admirecomment']) && in_array($request['admirecomment'], $privacy_types)){
				switch($request['admirecomment']){
					case 'specific':
						unset($request['admirecommentignore']);
						break;
					case 'private':
						unset($request['admirecommentignore']);
						unset($request['admirecommentspecific']);
						break;
					default:
						unset($request['admirecommentspecific']);
				}
				
				$privacy['testy']=$request['admirecomment'];
			}
			if(!empty($request['admirecommentspecific'])){
				$privacy['testyspecificpeople']=serialize(explode(',',$request['admirecommentspecific']));
			}
			if(!empty($request['admirecommentignore'])){
				$privacy['testyignore']=serialize(explode(',',$request['admirecommentignore']));
			}
			}
			
			
			if(in_array('videos', $privacy_parameters)){
			if(isset($request['videoview']) && in_array($request['videoview'], $privacy_types)){
				switch($request['videoview']){
					case 'specific':
						unset($request['videoviewhidden']);
						break;
					case 'private':
						unset($request['videoviewhidden']);
						unset($request['videoviewspecific']);
						break;
					default:
						unset($request['videoviewspecific']);
				}
				$privacy['videovisi']=$request['videoview'];
			}
			if(!empty($request['videoviewspecific'])){
				$privacy['videospeci']=serialize(explode(',',$request['videoviewspecific']));
			}
			if(!empty($request['videoviewhidden'])){
				$privacy['videohidden']=serialize(explode(',', $request['videoviewhidden']));
			}
			if(!empty($request['videocomment']) && in_array($request['videocomment'], $privacy_types)){
				switch($request['videocomment']){
					case 'specific':
						unset($request['videocommentignore']);
						break;
					case 'private':
						unset($request['videocommentignore']);
						unset($request['videocommentspecific']);
						break;
					default:
						unset($request['videocommentspecific']);
				}
				$privacy['video']=$request['videocomment'];
			}
			if(!empty($request['videocommentspecific'])){
				$privacy['videospecificpeople']=serialize(explode(',',$request['videocommentspecific']));
			}
			if(!empty($request['videocommentignore'])){
				$privacy['videoignore']=serialize(explode(',',$request['videocommentignore']));
			}
			}

			
			if(in_array('images', $privacy_parameters)){
			if(isset($request['imageview']) && in_array($request['imageview'], $privacy_types)){
				switch($request['imageview']){
					case 'specific':
						unset($request['imageviewhidden']);
						break;
					case 'private':
						unset($request['imageviewhidden']);
						unset($request['imageviewspecific']);
						break;
					default:
						unset($request['imageviewspecific']);
				}
				$privacy['albumvisi']=$request['imageview'];
			}
			if(!empty($request['imageviewspecific'])){
				$privacy['albumspeci']=serialize(explode(',',$request['imageviewspecific']));
			}
			if(!empty($request['imageviewhidden'])){
				$privacy['albumhidden']=serialize(explode(',', $request['imageviewhidden']));
			}
			if(!empty($request['imagecomment']) && in_array($request['imagecomment'], $privacy_types)){
				switch($request['imagecomment']){
					case 'specific':
						unset($request['imagecommentignore']);
						break;
					case 'private':
						unset($request['imagecommentignore']);
						unset($request['imagecommentspecific']);
						break;
					default:
						unset($request['imagecommentspecific']);
				}
				$privacy['album']=$request['imagecomment'];
			}
			
			if(!empty($request['imagecommentspecific'])){
				$privacy['albumspecificpeople']=serialize(explode(',',$request['imagecommentspecific']));
			}
			if(!empty($request['imagecommentignore'])){
				$privacy['albumignore']=serialize(explode(',',$request['imagecommentignore']));
			}
			}
			
			
			if(in_array('messages', $privacy_parameters)){
			if(!empty($request['message']) && in_array($request['message'], $privacy_types)){
				switch($request['message']){
					case 'specific':
						unset($request['messageignore']);
						break;
					case 'private':
						unset($request['messageignore']);
						unset($request['messagespecific']);
						break;
					default:
						unset($request['messagespecific']);
				}
				$privacy['message']=$request['message'];
			}
				
			if(!empty($request['messagespecific'])){
				$privacy['messagespecificpeople']=serialize(explode(',',$request['messagespecific']));
			}
				
			if(!empty($request['messageignore'])){
				$privacy['messageignore']=serialize(explode(',',$request['messageignore']));
			}
			}	
				
			
			//print_r($privacy);
			$privacymodel=new Application_Model_Privacy($this->registry->DB);
			$privacymodel->updatePrivacy($privacy);
			
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
	}
	
	public function updateinfoprivacyAction(){
		$this->_helper->viewRenderer->setNoRender();
		$data=$this->getRequest()->getParams();
		if(isset($this->authIdentity) && !empty($data['types'])){
			$privacy=new Application_Model_Privacy($this->registry['DB']);
			$privacy->updateinfoprivacy($data);
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
	}
	public function getskillsAction(){
			$getskills=new Application_Model_UserInfo($this->registry['DB']);
			$skills=$this->getRequest()->getParam('key');
			$this->view->results=$getskills->getskills($skills);
	}
	
}

