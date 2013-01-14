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
                <div class="span4 offset1">
                     <!-- image -->
                     BIG_IMAGE_COLUMN_1
                </div> <!-- column:1 -->

                <div class="span4">
                    <div style="pt100">
                        <h3> what is favesales</h3>
                        <p class="comment-text">
                            Favsales helps businesses to sell directly to fans through Facebook comment. 
                            It is simple to use and registration takes 30 secs!!!

                        </p>
                        <h3> How it benefits your business</h3>
                        <ul class="comment-text">
                            <li> Facebook comments increase sales as impulse buying increases. This is better than posting a
website or other link on Facebook.</li>
                            <li> You can seamlessly sell to their existing fans and friends who are more likely to browse and
purchase from their pages </li>
                            <li> You can list unsold inventory even though you sell via other channels </li>
                            <li> You can get an advantage of selling to better audience on your Facebook fan pages than other
channels.</li>
                            <li> You can easily run an auction for limited items </li>
                            <li> For buyers purchase friction is reduced as they do not have to figure out where and how to
purchase the products </li>
                            <li>Reduced cost for merchants compared to other ecommerce stores </li>
                            <li> Better than Facebook stores as this is integrated seamlessly with the Fan page and the buyer
notices no difference. You can keep on posting on your fan page where you do not want to sell.
                            </li>
                            <li>Preorders can easily be taken and inventory managed better. </li>
                            <li> No lost sales as people can order and you get know the demand </li>
                            <li> Lead generation - You get to know people to whom you cannot supply and you can offer them
alternative products        </li>

                        </ul>


                    </div>
                   
                </div> <!-- column:2 -->

            </div> <!-- row:1 -->
            <div class="row">
                <div class="span8 offset1">
                    <h3> How it works </h3>
                    <div class="section">
                        HOW_IT_WORKS_IMAGE_HORIZONTAL
                    </div>
                </div>
            </div> <!-- row:2 -->
        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

