<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo G_APP_TAGLINE ?></title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
             <?php include(APP_WEB_DIR . '/app/inc/top/site.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="pt100">
                        <div class="widget">
                            <p class="lead">
                                To Request a demo <br>
                                Give us a call on +91 90070 15444 <br>
                                Or write to us at <a href="mailto:support@favsales.com">support@favsales.com</a> <br>
                            </p>
                        </div>
                    </div>
                </div>

            </div><!-- row:1 -->

        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

