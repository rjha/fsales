<?php

namespace com\indigloo\fs\job {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    
    use \com\indigloo\fs\dao as Dao ;

    class Comment {

        static function execute() {
        	// look @ fs_stream_tracker
        	// get post_id that have been updated after 
        	// fs_stream_tracker.updated_on
        	
        	// process a post 
        	// write to fs_stream_tracker.updated_on 

        	$streamDao = new Dao\Stream();
        	$stop = false ;
        	$limit = 25 ;

        	while(!$stop) {
        		$posts = $streamDao->getPosts($limit);
        		$num_records = sizeof($posts);

        		if($num_records < $limit) {
        			$stop = true ;
        		}

        		foreach($posts as $post) {
        			self::process($post);
        		}

        	}

        }

        static function process($post) {
        	// is d_bit = 0 ? populate fs_post, flip d_bit to 1 
        	// pull all comments between (last_ts => next_ts) using post_id
        	// After writing a comment : 
        	// update fs_stream.last_stream_ts = comment.time if comment.time > last_stream_ts
        	// After post processing update fs_stream_tracker.updated_on = post.updated_on
        	

        }
        
    }
}

?>
