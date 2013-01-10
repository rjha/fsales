<?php

    include ('fs-app.inc');
    include(APP_WEB_DIR . '/app/inc/header.inc');

    $qUrl = \com\indigloo\Url::tryBase64QueryParam('q', '/');
    $qUrl = base64_decode($qUrl);

    $gWeb = \com\indigloo\core\Web::getInstance();
    $message = $gWeb->find("fs.router.message",true);
    $message = empty($message) ? "" : $message ;
?>

<!DOCTYPE html>
<html>

    <head>
        <title> Redirect page</title>
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
                        <h2>Redirecting. Please wait...</h2>
                    </div>
                    <p class="text-error"> <?php echo $message; ?></p>
                    <div class="p20">
                        <img src="/css/asset/fs/fb_loader.gif" alt="ajax loader" />
                    </div>

                    <div class="p20">
                           <a class="btn b" href="/">Home</a>
                    </div>

                </div>
            </div>
        </div> <!-- container -->

        <script>
            window.setTimeout(function() {window.location.href = '<?php echo $qUrl; ?>'; }, 8000);
        </script>


        <div id="ft">
            <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>
        </div>

    </body>
</html>
