#!/usr/bin/php
<?php
include("fs-app.inc");
include(APP_CLASS_LOADER);
include(APP_WEB_DIR."/app/inc/global-error.inc");

use \com\indigloo\fs\api\Graph as GraphAPI ;
// error handler is still the global error handler
set_exception_handler('offline_exception_handler');


\com\indigloo\fs\job\Stream::execute();
sleep(5);
\com\indigloo\fs\job\Comment::execute();

?>