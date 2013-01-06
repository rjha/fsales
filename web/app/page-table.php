
<!DOCTYPE html>
<html>

    <head>
        <title> User pages</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h2>Your pages</h2>
                    </div>
                    <p class="comment-text">
                        We found the pages listed below.
                        Please select the pages you want to monitor.
                    </p>
                    <?php \com\indigloo\ui\form\Message::render() ?>
                     
                    <form name="web-form1" action="/app/action/page/store.php" method="POST">
                        
                        <?php echo $pageTableHtml ; ?>
                        <div class="section">
                            <button class="btn btn-primary" type="submit" name="select" value="Select">Select</button>
                            &nbsp;
                            <a class="btn" href="/app/action/page/remove-session.php">Cancel</a>
                        </div>
                    </form>
                     
                </div>

            </div>

            
        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
