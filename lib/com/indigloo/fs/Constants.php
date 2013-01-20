<?php
namespace com\indigloo\fs  {

    class Constants {

        const SECRET_KEY = "SECRET_B00B5" ;

    	const COMMENT_VERB = "buyit" ;
        const ALL_COMMENT_FILTER = "all";
        const VERB_COMMENT_FILTER = "verb";
        
        const INVOICE_NEW_STATE = 1 ;
    	const INVOICE_PENDING_STATE = 2 ;
        const INVOICE_PROCESSING_STATE = 3 ;
        const INVOICE_PAID_STATE = 4 ;
        const INVOICE_SHIPPED_STATE = 5 ;



    	const DASHBOARD_URL = "/ghost/canvas/dashboard";
    	const ROUTER_URL = "/app/router.php";
    	const SELECT_PAGE_URL = "/ghost/canvas/select-page";
    	const LOGIN_URL = "/ghost/canvas/login";

    	const WWW_LOGIN_URL = "http://www.favsales.com/ghost/canvas/login";
    	const WWW_LOGIN_ERROR_URL = "http://www.favsales.com/ghost/canvas/login-error";
        const INVOICE_ALL_URL = "/app/invoice/all.php";
        const NEW_INVOICE_URL = "/app/invoice/new.php" ;
        const EDIT_INVOICE_URL = "/app/invoice/edit.php" ;
        const FACEBOOK_PROFILE_URL = "http://www.facebook.com/profile.php?id=%s" ;
        const WWW_CHECKOUT_URL = "http://www.favsales.com/app/pub/checkout.php";


    }

}

?>
