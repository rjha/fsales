<?php

    use \com\indigloo\fs\auth\Login as Login;
    $loginId = Login::tryLoginIdInSession();
    
    if(!empty($loginId)) {
        include(APP_WEB_DIR.'/app/inc/top/user.inc') ;
    }

?>