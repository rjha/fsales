<?php
    require_once ('fs-app.inc');
    require_once (APP_WEB_DIR.'/app/inc/header.inc');
    require_once (APP_WEB_DIR.'/app/inc/role/user.inc');

    
?>

<!DOCTYPE html>
<html>

    <head>
        <title> Welcome to favsales</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>

        <script>
            window.setTimeout(function() {window.location.href = '/ghost/canvas/dashboard'; }, 8000);
        </script>

    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h3>Welcome to favsales</h3>
                    </div>
                    <p class="lead">
                        
                        Congratulations. Your account has been setup with Favsales.
                        You can start monitoring comments on your facebook 
                        page now. Redirecting...   
                    </p>
                    <div class="p10">
                        <img src="/css/asset/fs/fb_loader.gif" alt="ajax loader" />
                    </div>

                </div>
            </div>
            
        </div>  <!-- container -->      

      

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
