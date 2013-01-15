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
                        <div class="section">
                            <h3> Timeline comments </h3>
                            <br>
                            <p class="comment-text"> 1. Your fans buy by commenting on your post </p>
                        </div>

                         <div class="section">
                            <h3> Comments dashboard </h3>
                            <br>
                            <p class="comment-text"> 2. All comments appear on your Favsales dashboard </p>
                        </div>

                        <div class="section">
                            <h3> Invoice</h3>
                            <br>
                            <p class="comment-text"> 3. You can send invoice for comments through Favsales</p>
                        </div>


                        <div class="section">
                            <h3> Payment</h3>
                            <br>
                            <p class="comment-text"> 4. Fans pay for item on Favsales app. 
                                Favsales manages credit card and netbanking payments.</p>
                        </div>


                    </div>
                </div>

            </div><!-- row:1 -->

        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

