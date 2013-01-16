
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
                    <div class="box-shadow p20 mt20">
                        <!-- image -->
                        <img src="/site/page/images/home-image_720_362.png" class="aligncenter" alt="main-image" />
                    </div>
                </div>

            </div> <!-- row:1 -->

            <div class="row">

                <div class="span8 offset1">
                   
                    <h3> what is Favsales</h3>
                    <p class="lead">
                        Favsales helps businesses to sell directly to fans through Facebook comment. 
                        It is simple to use and no setup is required.

                    </p>
                </div>
            </div> <!-- row:2 -->
            <div class="row">
                <div class="span8 offset1">
                    <h3> How it works </h3>
                    <div class="wrapper">
                        <div class="stack">
                            <span class="badge badge-warning">1</span>
                            Upload items on facebook
                            <br>
                            <img src="/site/page/images/facebook_220_167.png" alt="facebook-image"/>
                        </div>
                         <div class="stack">
                            <span class="badge badge-warning">2</span>
                            Your fans purchase by commenting on posts.
                            <br>
                            <img src="/site/page/images/buyit_220_112.png" alt="buy-it-image"/>
                        </div>
                         <div class="stack">
                            <span class="badge badge-warning">3</span>
                            Favsales does the processing and your receive 
                            payment in your bank account!
                            <br>
                            <img src="/site/page/images/explain-34.jpg" alt="money-image" />
                        </div>
                        <div class="clear">&nbsp; </div>

                    </div>
                    
                </div>
            </div> <!-- row:3 -->


            <div class="row">
                <div class="span8 offset1">
                    <h3> How it benefits your business</h3>
                    <div class="hr-red">&nbsp;</div>

                    <p class="lead">
                     Facebook comments increase sales as impulse buying increases. 
                      
                    </p>
                    <p class="lead">
                     Sell to existing fans and friends who are more 
                     likely to browse and purchase from your Facebook pages
                    </p>
                    <p class="lead">
                        Many other sales activities like auction, 
                        end of season sale, preorders and 
                        flash sales can easily be done.
                    </p>
                    <div class="widget">
                        <p class="lead">
                            To buy or to know more send an email to 
                            <a href="mailto:support@favsales.com">support@favsales.com</a>
                        </p>

                    </div>
                </div>
            </div> <!-- row:4 -->


        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

