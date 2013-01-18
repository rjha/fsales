<?php
    require_once ('fs-app.inc');
    require_once (APP_WEB_DIR.'/app/inc/header.inc');
    require_once (APP_WEB_DIR.'/app/inc/role/user.inc');

    
?>

<!DOCTYPE html>
<html>

    <head>
        <title> User account setup</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h3>Account setup error</h3>
                    </div>
                    <p class="lead">
                        
                        Errr. We encountered some errors during this account setup.
                        Please sign out and try to sign in again!
                    </p>
                </div>
            </div>
            
        </div>  <!-- container -->      

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
