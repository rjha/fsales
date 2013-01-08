<?php

namespace com\indigloo\fs\job {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    
    use \com\indigloo\fs\dao as Dao ;
    use \com\indigloo\fs\api\Graph as GraphAPI;

    class Comment {

        static function execute() {
        	// look @ fs_stream_tracker
        	// get 10 oldest posts

        	$streamDao = new Dao\Stream();
        	$limit = 10 ;
    		$posts = $streamDao->getPosts($limit);

    		foreach($posts as $post) {
    			self::process($post);
    		}

        }

        static function process($post) {

        	$postId = $post["post_id"];
        	$sourceId = $post["source_id"];
        	$d_bit = (int) $post["d_bit"];

        	$loginDao = new Dao\Login();
        	$streamDao = new Dao\Stream();

        	$token = $loginDao->getValidTokenOnSource($sourceId);

        	// is d_bit = 0 ? 
        	if($d_bit == 0) {
        		
        		$postDao = new Dao\Post();
        		// @todo : error handling for invalid token
        		$fbPost = GraphAPI::getPost($postId,$token);
        		// populate fs_post, flip d_bit to 1 
        		$postDao->add($sourceId,$postId,$fbPost);
        	}

        	self::pull_comments($sourceId,$postId,$token);
        	
        }
        
        static function pull_comments($sourceId,$postId,$token) {

        	// use FQL to fetch comments on object_id and ts1
        	$postDao = new Dao\Post();
        	$streamDao = new Dao\Stream();
        	$commentDao = new Dao\Comment();

        	$objectId = $postDao->getObjectId($postId);
        	$ts1 = $streamDao->getLastTS($postId);
        	
        	// pull N comments using FQL sorted by created_time
        	$limit = 10 ;
        	$fbComments = GraphAPI::getComments($objectId,$ts1,$limit,$token);
        	// store source_id + post_id + comment
        	// update fs_stream.last_stream_ts = comment.time
        	$commentDao->add($sourceId,$postId,$fbComments);

        }
    }
}

?>
