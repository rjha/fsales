<?php
namespace com\indigloo\fs\util{

    use \com\indigloo\Configuration as Config ;

    /* 
     * @see also
     * http://php.net/manual/en/language.namespaces.fallback.php
     * when PHP encounters a non-qualified class name - it assumes current namespace
     * when PHP encounters a non-qualified function name - it will fallback to global 
     * definition. 
     * Good practice is to qualify all class/function names.
     * 
     */
    class Asset {

        private static function getMinified($path) {
            $parts = pathinfo($path);
            //dir/file.min.extension 
            $template = "%s/%s.min.%s" ;
            $fname = sprintf($template,$parts["dirname"],$parts["filename"],$parts["extension"]);
            return $fname ;
        }

        static function version($path) {
            $link = '' ;
            $fname = self::getMinified($path) ;
            
            $parts = \pathinfo($path);
            if(\strcasecmp($parts["extension"],"css") == 0 ) {
                $tmpl = '<link rel="stylesheet" type="text/css" href="{fname}" >' ;
                $link = \str_replace("{fname}",$fname,$tmpl);
            }

            if(\strcasecmp($parts["extension"],"js") == 0 ) {
                $tmpl = '<script type="text/javascript" src="{fname}"></script>' ;
                $link = \str_replace("{fname}",$fname,$tmpl);
            }

            
            return $link ;
        }

    }

}
?>
