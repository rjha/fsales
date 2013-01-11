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
	    $fbObject = json_decode($post_body);

	    // check for true | false | null ?
	    if(is_object($fbObject) && property_exists($fbObject, "entry")){
		    $data = $fbObject->entry ;
		    foreach($data as $part) {
		       Logger::getInstance()->info("id = " .$part->id);
		       Logger::getInstance()->info("time = " .$part->time);

		       $fields = $part["changed_fields"];
		       foreach($fields as $field) {
	               Logger::getInstance()->info("field = " .$field);
		       }

			}
		}
  }
?>
