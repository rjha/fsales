<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use \com\indigloo\fs\dao as Dao ;
    use \com\indigloo\Logger ;

    $verify_token = 'FB_9421_B00B5';

 	if ($_SERVER['REQUEST_METHOD'] == 'GET' 
 		&& isset($_GET['hub_mode'])
   		&& $_GET['hub_mode'] == 'subscribe' ) {

 		// echo back hub.challenge
 		// @see https://developers.facebook.com/docs/reference/api/realtime/
		echo $_GET['hub_challenge'];

  } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  	Logger::getInstance()->info("received a ping from facebook ");

    $post_body = @file_get_contents('php://input');
    $fbObject = json_decode($post_body, true);
    $data = $fbObject->entry ;
    foreach($data as $part) {
    	Logger::getInstance()->info("uid = " .$part->uid);
    	Logger::getInstance()->info("time = " .$part->time);

    	$fields = $part["changed_fields"];
    	foreach($fields as $field) {
    		Logger::getInstance()->info("field = " .$field);
    	}

    }

    // $fbObject contains the list of fields that have changed
    /*
    
    $facebookDao = new Dao\Facebook();
    $facebookDao->addPing($data);
	*/


  }
?>
