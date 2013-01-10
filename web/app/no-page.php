
<!DOCTYPE html>
<html>

    <head>
        <title> User Facebook pages</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h3>No pages found</h3>
                    </div>

                    <?php \com\indigloo\ui\form\Message::render() ?>

                    <p class="lead">
                        We did not find any facebook pages for your account. This also happens
                        if your facebook session has expired. Clicking on <b>Sign in</b> should
                        fix the session issue.
                    </p>
                    

                    <?php echo $pageHtml ; ?>
                    <div class="section">
                        <a class="btn btn-success btn-large" href="/app/browser/login.php">Sign in</a>
                        
                    </div>
                </div>

            </div>

            
        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
