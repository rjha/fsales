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
                            <img src="/site/page/images/rjha-mug.jpg" class="alignleft" alt="rjha-mugshot" width="96" height="96"/>
                            Rajeev Jha has worked with Citrix, Americal Online, Oracle and Mahindra BT. He was
                            also part of startups Indegene and Everypath. He is
                            an alumnus of IIT Kanpur and studied at IIM Bangalore.
                        </div>
                        <div class="section">
                             <img src="/site/page/images/ss-mug_96_96.png" class="alignleft" alt="ss-mugshot" width="96" height="96" />
                             Saurabh Srivastava has earilier worked with Infosys, Franklin Templeton 
                             ,Ness Technologies and TCG Software. He has consulted 7to9 retail and "Show Off", 
                             an apparel retailer. He is an alumnus of IIT Kanpur and studied at IIM Calcutta.
                        </div>
                        
                        <div class="section">
                            
                            Sanjeev Jha is an alumni of Malnad college of Engineering. 
                            He has worked with Accenture and ran a startup called Indigloo.com in the past.
                        </div>

                    </div>
                </div>

            </div><!-- row:1 -->

        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

