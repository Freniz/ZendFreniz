<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function _initCustomBootstrap(){
		
		
	/** Zend Db Initialization */
	$registry=Zend_Registry::getInstance();
	$config=array(
				'username'	=>'XXXX',
				'password'  =>'XXXX',
				'dbname'	=>'fztest1'
               );
	$DB=new Zend_Db_Adapter_Pdo_Mysql($config);
	$registry->set('DB', $DB);
	$registry->set('limit',20);
	$registry->set('commentDefaultLimit', 3);
	$registry->set('commentlimt', 50);
	$registry->set('pageCategories',array('school','college'));
	$registry->set('onlineusers',array());
	$registry->set('defaultLists',array('fashion'));
	$registry->set('defaultListIds',array('13'));
	
	}
	public function _initCustomRoute(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$router->addRoute(
				'user',
				new Zend_Controller_Router_Route(':userid/',
						array('controller' => 'profile',
								'action' => 'index')));
		$router->addRoute(
				'albums',
				new Zend_Controller_Router_Route(':userid/albums/',
						array('controller' => 'albums',
								'action' => 'albums'))
		);
		/*$router->addRoute(
				'user1',
				new Zend_Controller_Router_Route('/:userid/:tab/',
						array('controller' => 'profile',
								'action' => 'index')));*/
		$router->addRoute(
				'login',
				new Zend_Controller_Router_Route('login/*',
						array('controller' => 'index',
								'action' => 'login'))
		);
		$router->addRoute(
				'frenizsearch',
				new Zend_Controller_Router_Route('frenizsearch/*',
						array('controller' => 'search',
								'action' => 'frenizsearch'))
		);
		$router->addRoute(
				'loginattempt',
				new Zend_Controller_Router_Route('loginattempt',
						array('controller' => 'index',
								'action' => 'loginattempt'))
		);
		$router->addRoute(
				'resetpassword',
				new Zend_Controller_Router_Route('resetpassword',
						array('controller' => 'index',
								'action' => 'resetpassword'))
		);
		$router->addRoute(
				'signupaccount',
				new Zend_Controller_Router_Route('signupaccount',
						array('controller' => 'index',
								'action' => 'signupaccount'))
		);
		$router->addRoute(

				'getuserstature',

				new Zend_Controller_Router_Route('getuserstatures/',

						array('controller' => 'statures',

								'action' => 'getuserstatures'))

		);
		$router->addRoute(
				'inbox',
				new Zend_Controller_Router_Route('messages/:userid/',
						array('controller' => 'messages',
								'action' => 'messages'))
		);
		$router->addRoute(
				'developer',
				new Zend_Controller_Router_Route('developers/',
						array('controller' => 'developers',
								'action' => 'developers'))
		);
		$router->addRoute(
				'hire',
				new Zend_Controller_Router_Route('hireus/',
						array('controller' => 'developers',
								'action' => 'hireus'))
		);
		$router->addRoute(
				'getuser',
				new Zend_Controller_Router_Route('messages/',
						array('controller' => 'messages',
								'action' => 'messages'))
		);
		
		$router->addRoute(
		
				'deletemess',
		
				new Zend_Controller_Router_Route('deletemessage/*',
		
						array('controller' => 'messages',
		
								'action' => 'deletemessage'))
		
		);
		$router->addRoute(

				'sendmess',

				new Zend_Controller_Router_Route('sendmessages/*',

						array('controller' => 'messages',

								'action' => 'sendmessages'))

		);
		
		
		
	
		$router->addRoute(

				'sentmess',

				new Zend_Controller_Router_Route('messages/sentmessages/*',

						array('controller' => 'messages',

								'action' => 'sentmessages'))

		);

		$router->addRoute(

				'getinvites',

				new Zend_Controller_Router_Route('getinvites/*',

						array('controller' => 'invites',

								'action' => 'getinvites'))

		);

		

		
		$router->addRoute(

				'getuserblogs',

				new Zend_Controller_Router_Route('blog/:userid',

						array('controller' => 'blog',

								'action' => 'blog'))

		);
		$router->addRoute(

				'addblog',

				new Zend_Controller_Router_Route('addblog/',

						array('controller' => 'blog',

								'action' => 'addblog'))

		);
		
		$router->addRoute(

				'getuseradmire',

				new Zend_Controller_Router_Route('admire/:userid',

						array('controller' => 'admire',

								'action' => 'admire'))

		);
		$router->addRoute(

				'addadmiration',

				new Zend_Controller_Router_Route('addadmiration/',

						array('controller' => 'admire',

								'action' => 'addadmiration'))

		);
		$router->addRoute(

				'voteadmire',

				new Zend_Controller_Router_Route('voteadmire/',

						array('controller' => 'admire',

								'action' => 'voteadmire'))

		);
		$router->addRoute(

				'unvoteadmire',

				new Zend_Controller_Router_Route('unvoteadmire/',

						array('controller' => 'admire',

								'action' => 'unvoteadmire'))

		);
		$router->addRoute(

				'accountsetting',

				new Zend_Controller_Router_Route('accountsettings/',

						array('controller' => 'settings',

								'action' => 'accountsettings'))

		);
		$router->addRoute(

				'updatebasicinfo',

				new Zend_Controller_Router_Route('updatebasicinfo/',

						array('controller' => 'settings',

								'action' => 'updatebasicinfo'))

		);
		$router->addRoute(

				'updatepersonalinfo',

				new Zend_Controller_Router_Route('updatepersonalinfo/',

						array('controller' => 'settings',

								'action' => 'updatepersonalinfo'))

		);
		$router->addRoute(

				'updatetofavorites',

				new Zend_Controller_Router_Route('updatetofavorites/',

						array('controller' => 'settings',

								'action' => 'updatetofavorites'))

		);
		$router->addRoute(

				'updatediary',

				new Zend_Controller_Router_Route('updatediary/',

						array('controller' => 'apps',

								'action' => 'updatediary'))

		);
		$router->addRoute(

				'updateslambook',

				new Zend_Controller_Router_Route('updateslambook/',

						array('controller' => 'apps',

								'action' => 'updateslambook'))

		);
		$router->addRoute(

				'getdiary',

				new Zend_Controller_Router_Route('diary/',

						array('controller' => 'apps',

								'action' => 'diary'))

		);
		$router->addRoute(

				'getslambook',

				new Zend_Controller_Router_Route('slambook/',

						array('controller' => 'apps',

								'action' => 'slambook'))

		);
		$router->addRoute(
		
				'addslambook',
		
				new Zend_Controller_Router_Route('addslambook/:userid',
		
						array('controller' => 'apps',
		
								'action' => 'addslambook'))
		
		);
		$router->addRoute(

				'getimages',

				new Zend_Controller_Router_Route('getimages/',

						array('controller' => 'image',

								'action' => 'getimages'))

		);
		
		$router->addRoute(

				'getuservideos',

				new Zend_Controller_Router_Route('videos/:userid',

						array('controller' => 'videos',

								'action' => 'videos'))

		);
		$router->addRoute(

				'getvideos',

				new Zend_Controller_Router_Route('getvideos/',

						array('controller' => 'videos',

								'action' => 'getvideos'))

		);
		$router->addRoute(

				'videocomment',

				new Zend_Controller_Router_Route('docommentvideo/',

						array('controller' => 'videos',

								'action' => 'docommentvideo'))

		);
		$router->addRoute(
		
				'deletevideocomment',
		
				new Zend_Controller_Router_Route('deletevideocomment/',
		
						array('controller' => 'videos',
		
								'action' => 'deletevideocomment'))
		
		);
		$router->addRoute(
		
				'deletevideo',
		
				new Zend_Controller_Router_Route('deletevideo/',
		
						array('controller' => 'videos',
		
								'action' => 'deletevideo'))
		
		);
		$router->addRoute(
		
				'addvideo',
		
				new Zend_Controller_Router_Route('addvideo/',
		
						array('controller' => 'videos',
		
								'action' => 'addvideo'))
		
		);
		$router->addRoute(

				'getbdy',

				new Zend_Controller_Router_Route('getfriendsbdy/',

						array('controller' => 'suggestions',

								'action' => 'getfriendsbdy'))

		);
		$router->addRoute(

				'votevideo',

				new Zend_Controller_Router_Route('votevideo/',

						array('controller' => 'videos',

								'action' => 'votevideo'))

		);
		$router->addRoute(

				'unvotevideo',

				new Zend_Controller_Router_Route('unvotevideo/',

						array('controller' => 'videos',

								'action' => 'unvotevideo'))

		);
		$router->addRoute(

				'getuserscribbles',

				new Zend_Controller_Router_Route('scribbles/',

						array('controller' => 'scribbles',

								'action' => 'scribbles'))

		);
		$router->addRoute(

				'docommentscribbles',

				new Zend_Controller_Router_Route('docommentscribbles/',

						array('controller' => 'scribbles',

								'action' => 'docommentscribbles'))

		);
		$router->addRoute(

				'addscribbles',

				new Zend_Controller_Router_Route('addscribbles/',

						array('controller' => 'scribbles',

								'action' => 'addscribbles'))

		);
		$router->addRoute(

				'deletescribbles',

				new Zend_Controller_Router_Route('deletescribbles/',

						array('controller' => 'scribbles',

								'action' => 'deletescribbles'))

		);
		$router->addRoute(

				'deletescribblescomment',

				new Zend_Controller_Router_Route('deletescribblescomment/',

						array('controller' => 'scribbles',

								'action' => 'deletescribblescomment'))

		);
		$router->addRoute(

				'votescribbles',

				new Zend_Controller_Router_Route('votescribbles/',

						array('controller' => 'scribbles',

								'action' => 'votescribbles'))

		);
		$router->addRoute(

				'unvotescribbles',

				new Zend_Controller_Router_Route('unvotescribbles/',

						array('controller' => 'scribbles',

								'action' => 'unvotescribbles'))

		);
		$router->addRoute(

				'voteblog',

				new Zend_Controller_Router_Route('voteblog/',

						array('controller' => 'blog',

								'action' => 'voteblog'))

		);
		$router->addRoute(

				'unvoteblog',

				new Zend_Controller_Router_Route('unvoteblog/',

						array('controller' => 'blog',

								'action' => 'unvoteblog'))

		);
		$router->addRoute(

				'deleteblog',

				new Zend_Controller_Router_Route('deleteblog/',

						array('controller' => 'blog',

								'action' => 'deleteblog'))

		);
		$router->addRoute(

				'deleteadmire',

				new Zend_Controller_Router_Route('deleteadmire/',

						array('controller' => 'admire',

								'action' => 'deleteadmire'))

		);
		$router->addRoute(

				'addstature',

				new Zend_Controller_Router_Route('addstatures/',

						array('controller' => 'statures',

								'action' => 'addstatures'))

		);
		$router->addRoute(

				'deletestature',

				new Zend_Controller_Router_Route('deletestature/',

						array('controller' => 'statures',

								'action' => 'deletestature'))

		);
		$router->addRoute(

				'dostaturecomment',

				new Zend_Controller_Router_Route('dostaturecomment/',

						array('controller' => 'statures',

								'action' => 'dostaturecomment'))

		);
		$router->addRoute(

				'deletestaturecomment',

				new Zend_Controller_Router_Route('deletestaturecomment/',

						array('controller' => 'statures',

								'action' => 'deletestaturecomment'))

		);
		$router->addRoute(

				'votestature',

				new Zend_Controller_Router_Route('votestature/',

						array('controller' => 'statures',

								'action' => 'votestature'))

		);
		$router->addRoute(

				'unvotestature',

				new Zend_Controller_Router_Route('unvotestature/',

						array('controller' => 'statures',

								'action' => 'unvotestature'))

		);
		$router->addRoute(

				'getstreams',

				new Zend_Controller_Router_Route('streams/',

						array('controller' => 'streams',

								'action' => 'streams'))

		);
		$router->addRoute(

				'uploadimage',

				new Zend_Controller_Router_Route('uploadimage/',

						array('controller' => 'image',

								'action' => 'uploadimage'))

		);
		$router->addRoute(

				'photos',

				new Zend_Controller_Router_Route('image/:albumid',

						array('controller' => 'image',

								'action' => 'image'))

		);
		$router->addRoute(
		
				'pinnedpics',
		
				new Zend_Controller_Router_Route('image/pinnedpics/:userid',
		
						array('controller' => 'image',
		
								'action' => 'image',
								'albumid' => 'pinnedpics' ))
		
		);
		$router->addRoute(

				'crop',

				new Zend_Controller_Router_Route('crop/:imageid',

						array('controller' => 'image',

								'action' => 'crop'))

		);
		$router->addRoute(

				'setpropic',

				new Zend_Controller_Router_Route('setprofilepicture',

						array('controller' => 'image',

								'action' => 'setprofilepicture'))

		);
		$router->addRoute(

				'setsecpic',

				new Zend_Controller_Router_Route('setsecondarypicture',

						array('controller' => 'image',

								'action' => 'setsecondarypicture'))

		);
		$router->addRoute(

				'createforum',

				new Zend_Controller_Router_Route('createforum',

						array('controller' => 'forum',

								'action' => 'createforum'))

		);
		$router->addRoute(

				'askquestion',

				new Zend_Controller_Router_Route('askquestion',

						array('controller' => 'forum',

								'action' => 'askquestion'))

		);
		$router->addRoute(

				'ansquestion',

				new Zend_Controller_Router_Route('ansquestion',

						array('controller' => 'forum',

								'action' => 'ansquestion'))

		);
		$router->addRoute(

				'commentans',

				new Zend_Controller_Router_Route('commentanswer',

						array('controller' => 'forum',

								'action' => 'commentanswer'))

		);
		$router->addRoute(

				'editquestion',

				new Zend_Controller_Router_Route('editquestion',

						array('controller' => 'forum',

								'action' => 'editquestion'))

		);
		$router->addRoute(

				'editanswer',

				new Zend_Controller_Router_Route('editanswer',

						array('controller' => 'forum',

								'action' => 'editanswer'))

		);
		$router->addRoute(

				'editcomment',

				new Zend_Controller_Router_Route('editcomment',

						array('controller' => 'forum',

								'action' => 'editcomment'))

		);
		$router->addRoute(

				'votequestion',

				new Zend_Controller_Router_Route('votequestion',

						array('controller' => 'forum',

								'action' => 'votequestion'))

		);
		$router->addRoute(

				'voteanswer',

				new Zend_Controller_Router_Route('voteanswer',

						array('controller' => 'forum',

								'action' => 'voteanswer'))

		);
		$router->addRoute(

				'getforum',

				new Zend_Controller_Router_Route('getforum',

						array('controller' => 'forum',

								'action' => 'getforum'))

		);
		$router->addRoute(

				'forum',

				new Zend_Controller_Router_Route('forum',

						array('controller' => 'forum',

								'action' => 'forum'))

		);
		$router->addRoute(

				'question',

				new Zend_Controller_Router_Route('question/:questionid/*',

						array('controller' => 'forum',

								'action' => 'question'))

		);
		$router->addRoute(

				'answer',

				new Zend_Controller_Router_Route('answers/:questionid/*',

						array('controller' => 'forum',

								'action' => 'answers'))

		);
		
		$router->addRoute(

				'topic',

				new Zend_Controller_Router_Route('topic',

						array('controller' => 'forum',

								'action' => 'topic'))

		);
		$router->addRoute(

				'addpin',

				new Zend_Controller_Router_Route('addpin',

						array('controller' => 'image',

								'action' => 'addpin'))

		);
		$router->addRoute(

				'pinmereq',

				new Zend_Controller_Router_Route('pinmereq',

						array('controller' => 'image',

								'action' => 'pinmereq'))

		);
		$router->addRoute(

				'getpinmereq',

				new Zend_Controller_Router_Route('getpinmereq',

						array('controller' => 'image',

								'action' => 'getpinmereq'))

		);
		$router->addRoute(

				'getpinreq',

				new Zend_Controller_Router_Route('getpinreq',

						array('controller' => 'image',

								'action' => 'getpinreq'))

		);
		$router->addRoute(

				'tobereview',

				new Zend_Controller_Router_Route('tobereview',

						array('controller' => 'reviews',

								'action' => 'tobereview'))

		);
		$router->addRoute(

				'reviewpinmereq',

				new Zend_Controller_Router_Route('reviewpinmereq',

						array('controller' => 'reviews',

								'action' => 'reviewpinmereq'))

		);
		$router->addRoute(

				'reviewpinreq',

				new Zend_Controller_Router_Route('reviewpinreq',

						array('controller' => 'reviews',

								'action' => 'reviewpinreq'))

		);
		$router->addRoute(

				'adviews',

				new Zend_Controller_Router_Route('views',

						array('controller' => 'forum',

								'action' => 'views'))

		);
		$router->addRoute(

				'buildindex',

				new Zend_Controller_Router_Route('buildindex',

						array('controller' => 'search',

								'action' => 'buildindex'))

		);
		$router->addRoute(

				'search',

				new Zend_Controller_Router_Route('search',

						array('controller' => 'search',

								'action' => 'search'))

		);
		$router->addRoute('places',new Zend_Controller_Router_Route('/places/:placeid',array('controller' => 'places')));
		$router->addRoute(

				'placesearch',

				new Zend_Controller_Router_Route('placesearch',

						array('controller' => 'search',

								'action' => 'placesearch'))

		);
		$router->addRoute(

				'buildforum',

				new Zend_Controller_Router_Route('buildforum',

						array('controller' => 'build',

								'action' => 'buildforum'))

		);
		$router->addRoute(

				'buildusers',

				new Zend_Controller_Router_Route('buildusers',

						array('controller' => 'build',

								'action' => 'buildusers'))

		);
		$router->addRoute(

				'buildplaces',

				new Zend_Controller_Router_Route('buildplaces',

						array('controller' => 'build',

								'action' => 'buildplaces'))

		);
		$router->addRoute(

				'forumsearch',

				new Zend_Controller_Router_Route('forumsearch',

						array('controller' => 'search',

								'action' => 'forumsearch'))

		);
		$router->addRoute(

				'tagsearch',

				new Zend_Controller_Router_Route('gettags',

						array('controller' => 'search',

								'action' => 'gettags'))

		);
		$router->addRoute(

				'skillsearch',

				new Zend_Controller_Router_Route('getskills',

						array('controller' => 'search',

								'action' => 'getskills'))

		);
		$router->addRoute(
		
				'createleaf',
		
				new Zend_Controller_Router_Route('createleaf',
		
						array('controller' => 'leaf',
		
								'action' => 'createleaf'))
		
		);
		$router->addRoute(
		
				'notification',
		
				new Zend_Controller_Router_Route('notification',
		
						array('controller' => 'notification',
		
								'action' => 'notification'))
		
		);
		$router->addRoute(
		
				'leafedit',
		
				new Zend_Controller_Router_Route('leafedit/:leafid',
		
						array('controller' => 'leaf',
		
								'action' => 'leafedit'))
		
		);
	}
	protected function _initAutoloaders()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->setFallbackAutoloader(true);
		
		$default_loader = new Zend_Application_Module_Autoloader(array(
				'namespace' => '',
				'basePath'  => APPLICATION_PATH
		));
	}

}

