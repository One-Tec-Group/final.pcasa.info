<?php
include_once('pinterest_vendor/autoload.php');
use DirkGroenen\Pinterest\Pinterest;

class Pinterests {

		public $user_id="";
		public $app_id="";
		public $app_secret="";
		public $pinterest="";
		public $pinterest_config_table_id="";


	function __construct(){

		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");


		$pinterest_config = $this->CI->basic->get_data("pinterest_config",array('where'=>array("user_id"=> $this->user_id, 'deleted'=>'0', "status"=>"1")));

		if(isset($pinterest_config[0])) {

			$this->app_id=$pinterest_config[0]["client_id"];
			$this->app_secret=$pinterest_config[0]["client_secret"];
			$this->pinterest_config_table_id=$pinterest_config[0]["id"];
		}

		// $this->app_id="4899848561142808486";
		// $this->app_secret="af1e6a35a22c3dac8bcab10b63cafbbc8f0ac9e71b46e22768c40fe145b68262";

		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}


		$this->pinterest = new Pinterest($this->app_id, $this->app_secret);
	}

	
	public function app_initialize($pinterest_config_table_id)
	{	  
	    $pinterest_config = $this->CI->basic->get_data("pinterest_config",array('where'=>array('id'=>$pinterest_config_table_id)));
		if(isset($pinterest_config[0]))
		{			
			$this->app_id=$pinterest_config[0]["client_id"];
			$this->app_secret=$pinterest_config[0]["client_secret"];
		}	
		if (session_status() == PHP_SESSION_NONE) 
		{
		    session_start();
		}
		$this->pinterest = new Pinterest($this->app_id, $this->app_secret);
	}



	public function login_button($redirect_uris)
	{
		$loginurl = $this->pinterest->auth->getLoginUrl($redirect_uris, array('read_public','write_public','read_relationships','write_relationships'));

		if ($this->app_id == '' || $this->app_secret == '') {
			$loginurl = base_url('social_apps/pinterest_settings'); 
		}

		return $loginurl;

        // return "<a href='{$loginurl}' class='btn btn-outline-primary login_button' social_account='pinterest'><i class='fas fa-plus-circle'></i> ".$this->CI->lang->line("Import Account")."</a>";
	}

	public function get_userinfo($code)
	{
		if (!isset($this->app_id) || !isset($this->app_secret)) {

		    $this->CI->session->set_userdata('account_import_error', $this->CI->lang->line("App Id or App Secret has not set yet. Please set it on: <a href='".base_url("social_apps/pinterest_settings")."'>Here</a>"));
			redirect(base_url('comboposter/social_accounts'),'refresh');
		}

		$token = $this->pinterest->auth->getOAuthToken($code);
    	$this->pinterest->auth->setOAuthToken($token->access_token);

    	$userInfo = $this->pinterest->users->me(array('fields' => 'username,first_name,last_name,counts,image[small,large]'));
    	$userInfoArray = $this->accessProtected($userInfo,"attributes");

    	/**
    	 * set user infos
    	 */
    	$userName = '';
    	$name = '';
    	$image = base_url('assets/images/pinterest.jpg');
    	$pins = 0;
    	$boards = 0;

    	if (isset($userInfoArray['username'])) {
			$userName = $userInfoArray['username'];
    	}

    	if (isset($userInfoArray['first_name']) && isset($userInfoArray['last_name'])) {
			$name = $userInfoArray['first_name']. ' '. $userInfoArray['last_name'];
    	}

		if (isset($userInfoArray['image']['large']['url'])) {
			$image = $userInfoArray['image']['large']['url'];
		}

		if (isset($userInfoArray['counts']['pins'])) {
			$pins = $userInfoArray['counts']['pins'];
		}

		if (isset($userInfoArray['counts']['boards'])) {
			$boards = $userInfoArray['counts']['boards'];
		}

		$this->CI->session->set_userdata('pinterest_username',$userName);
		$this->CI->session->set_userdata('pinterest_name',$name);
		$this->CI->session->set_userdata('pinterest_image',$image);
		$this->CI->session->set_userdata('pinterest_pins',$pins);
		$this->CI->session->set_userdata('pinterest_boards',$boards);
		$this->CI->session->set_userdata('pinterest_access_token',$token->access_token);

		/* ----- Start Board Name Url----- */

		$getMyBoards = $this->pinterest->users->getMeBoards();


		$reflector = new ReflectionObject($getMyBoards);
		$nodes = $reflector->getProperty('response');
		$nodes->setAccessible(true);
		$sortResponse = $nodes->getValue($getMyBoards);

		$reflector1 = new ReflectionObject($sortResponse);
		$bordResponse = $reflector1->getProperty('response');
		$bordResponse->setAccessible(true);
		$detailsRespose = $bordResponse->getValue($sortResponse);
		$bordInfo = $detailsRespose['data'];

		$arrayLen = count($bordInfo);
		$finalBoardName = array();

		for ($i=0; $i < $arrayLen; $i++) {
			$bordUrlWithSlash = $bordInfo[$i]['url'];
			$bordUrl = rtrim($bordUrlWithSlash, "/");
			$bordNameArray = explode("/", $bordUrl);
			$bordName = end($bordNameArray);
			$finalBoardName[] = $bordName;
			unset($bordUrlWithSlash, $bordUrl, $bordNameArray, $bordName);
		}

		return $finalBoardName;
	}

	public function accessProtected($userName, $attributes){
  		$reflection = new ReflectionClass($userName);
  		$property = $reflection->getProperty($attributes);
  		$property->setAccessible(true);
  		return $property->getValue($userName);
	}

	public function youtube_video_post_to_pinterest($username,$bordname,$video_url,$access_token,$description)
	{
    	$this->pinterest->auth->setOAuthToken($access_token);
			$video_id = $this->getVideoId($video_url);

		$post = $this->pinterest->pins->create(array(
	     	"note"          => $description,
			//"image_url"		=> "https://img.youtube.com/vi/t5jQRzVvSuM/0.jpg",
	     	"image_url"		=> "https://img.youtube.com/vi/".$video_id."/0.jpg",
	     	//"board"         => $userName."/".$finalBoardName[0]
	     	"board"         => $username."/".$bordname
		 ));
		$url = $this->accessProtected($post,'attributes');
		return $url;
	}

	public function image_post_to_pinterest($username,$bordname,$image_url,$access_token,$description)
	{
    $this->pinterest->auth->setOAuthToken($access_token);

		$post = $this->pinterest->pins->create(array(
	     	"note"          => $description,
			//"image_url"		=> "https://img.youtube.com/vi/t5jQRzVvSuM/0.jpg",
	     	"image_url"		=> $image_url,
	     	//"board"         => $userName."/".$finalBoardName[0]
	     	"board"         => $username."/".$bordname
		 ));
		$url = $this->accessProtected($post,'attributes');
		return $url;
	}


	public function getVideoId($url)
	{
			$url = $url;

			if (parse_url ($url,  PHP_URL_HOST) == "www.youtube.com") {
					$url = explode("=", $url);
					if (strpos($url[1], '&list') !== false) {
							return $video_id = str_replace("&list", '', $url[1]); //str_replace('search', 'replace', 'string' )
					}
					return $video_id = $url[1];
			}

			if (parse_url ($url,  PHP_URL_HOST) == "youtu.be") {
					$url = explode("/", $url);
					return $video_id = end($url); //end($array) return last array element
			}

			return "This is not valid youtube url.";
	}


}
