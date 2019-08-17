<?php
require_once 'src/Facebook/autoload.php'; // change path as needed

/**
 * Class to fetch posts from facebook page
 * By tasiukwaplong
 */
class FacebookPosts{
	private $pageId = '';
	private $appId = '';
	private $accessToken = '';
	private $appSecret = '';

	public $response = [];

	private $fb = '';

	public function __construct($pageId, $appId, $accessToken, $appSecret){
		  $this->fb = new \Facebook\Facebook([
			  'app_id' => $appId,
			  'app_secret' => $appSecret,
			  'default_graph_version' => 'v2.10',
			  'default_access_token' => $accessToken
		]);

		  $this->pageId = $pageId;
	}


public function getAllPosts(){
  # get all posts from the page
  try {
  // Returns a `Facebook\FacebookResponse` object  
  $response = $this->fb->get(
    "/".$this->pageId."/feed?fields=created_time,message,id,full_picture,picture&limit=100"
  );

	//print_r(json_decode($response->getBody(), true)) ;//in array
  $this->response = $response->getBody(); 
//	return $response->getBody();//in json
//test
	return $response->getBody();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
//  echo 'Graph returned an error: ' . $e->getMessage();
  return $this->setError('An error occured reading posts. We are working to fix that');
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
//  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  return $this->setError('An error occured reading posts. Try again later');
  exit;
}

}


public function getPost($postId){
	
	if (empty($postId) || !isset($postId)) {
	  return $this->setError("Post might be deleted or blocked by facebook");
	}

	  try {
	  // Returns a `Facebook\FacebookResponse` object
	  $response = $this->fb->get(
	    "/$postId"
	  );
	  //print_r(json_decode($response->getBody(), true)) ;//in array
	  return $response->getBody();//in json
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	 // echo 'Graph returned an error: ' . $e->getMessage();
	  return $this->setError('An error occured reading this post. We are working to fix that');
	  exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	//  echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  return $this->setError('An error occured reading this post.');
	  exit;
	}

}

public function setError($message){
  //send error to user
  return 
    json_encode([
      'data'=>[
        'created_time'=>'error',
        'message'=> $message,
        'id'=>'error'
      ]
    ]);
}


private function hasNextPost(){
	//check if next post exist
	$response = json_decode($this->response, true);//in array
	$this->nextPost = $response['paging']['next'];
	return array_key_exists('next', $response['paging']);
}


public function asArray($jsonData=''){
	$response = (isset($jsonData)) ? $jsonData : $this->response;
	//convert json to PHP array
	return json_decode($response, true);//in array	
}

}