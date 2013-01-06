
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
                        The application could not find any pages. This also happens
                        when your Facebook session expires. Clicking on "Sign in" should
                        fix this issue.
                    </p>
                    <?php \com\indigloo\ui\form\Message::render() ?>

                    <?php echo $pageHtml ; ?>
                    <div class="section">
                        <a class="btn btn-primary" href="/app/browser/login.php">Sign in</a>
                        
                    </div>
                </div>

            </div>

            
        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
