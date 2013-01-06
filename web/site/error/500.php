<?php

  $ui_message = "" ;
  
  if(array_key_exists("message", $_GET)) {
    $ui_message = $_GET["message"] ;
  } else {
    $ui_message = "we apologize for the inconvenience" ;
  }



?>

<!DOCTYPE html>
 <html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <head>
        <meta charset="utf-8">
        <title>500 Error</title>
</head>

<body>

<style>
html, body {
  height: 100%;
}
body {
  margin: auto;
  width: 1008px;
  background-color: #D4D9DD;
  font-family: Verdana, Arial, sans-serif;
}
a {
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
a:link, a:hover, a:visited {
  color: #136CB2;
}
a:active {
  color: #E7BE00;
}
#error {
  height: 100%;
  background-color: white;
  border-left: 1px solid #999999;
  border-right: 1px solid #999999;
  box-shadow: 0 0 5px 5px #C5CACD;
}
.error_message {
  color: #999999;
  font-size: 17.5px;
  padding: 30px 70px 20px;
}
.error_bubble {
  font-family: Arial, helvetica, sans-serif;
  font-weight: bold;
  border-radius: 8px;
  -moz-border-radius: 8px;
  margin: 0 70px 0 70px;
  padding: 50px;
}
.error_bubble div {
  display: inline-block;
  vertical-align: middle;
}
.error_quote {
  color: #FFFFFF;
  font-size: 30px;
  line-height: 1.35em;
  padding-left: 50px;
  width: 500px;
}
.error_code {
  font-size: 100px;
  line-height: 50px;
  margin-top:8px
}
.error_arrow {
  border-color: #DECA16 transparent transparent transparent;
  border-style: solid;
  border-width: 21px 0px 0px 32px;
  height: 0;
  left: 700px;
  line-height: 0;
  position: relative;
  top: -1px;
  width: 0;
}
.error_code > span {
  font-size: 50px;
}
.error_attrib {
  color: #999999;
  float: right;
  font-size: 14px;
  font-style: italic;
  padding: 10px 70px 36px;
}
.error_code_404 .error_bubble {
  background: none repeat scroll 0% 0% #DECA16;
  border: 1px solid #DECA16;
}
.error_code_500 .error_bubble {
  background: none repeat scroll 0% 0% #EDCA24;
  border: 1px solid #EDCA24;
}
.error_code_404 .error_code {
  color: #BFAD13;
}
.error_code_500 .error_code {
  color: #CCAD1F;
}
.error_code_500 .error_arrow {
  border-color: #EDCA24 transparent transparent transparent;
}
a.btn {
  background-repeat: repeat-x; 
  background-color: #ECE2C6;
  background-image: linear-gradient(rgba(255,255,255,.8) 5%, rgba(255,255,255,0.0) 70%, rgba(255,255,255,0.8) 100%); 
  background-image: -moz-linear-gradient(rgba(255,255,255,.8) 5%, rgba(255,255,255,0.0) 70%, rgba(255,255,255,0.8) 100%); 
  background-image: -webkit-linear-gradient(rgba(255,255,255,.8) 5%, rgba(255,255,255,0.0) 70%, rgba(255,255,255,0.8) 100%); 
  background-image: -ms-linear-gradient(rgba(255,255,255,.8) 5%, rgba(255,255,255,0.0) 70%, rgba(255,255,255,0.8) 100%); 
  border-color: #E0E0E0 #C0C0C0 #C0C0C0 #E0E0E0;
  border-radius: 3px 3px 3px 3px;
  border-style: solid;
  border-width: 1px;
  color: #000000;
  cursor: pointer;
  float: right;
  margin-right: 70px;
  margin-top: 26px;
  padding: 0.3em 0.6em;
  text-decoration: none;
}
a.btn:hover {
  background-image: none;
  border-color: #E6B800;
}
.clear {
  clear: both;
}
</style>


<div id="error" class="error_code_500">
    <a class="btn" href="mailto:support@favsales.com?subject=500%20Error">Report this</a>
    <div class="error_message">
        We had a problem processing that request. <a href="/">Go to the Homepage</a> »
    </div>
    <div class="error_bubble">
        <div class="error_code">500<br><span>ERROR</span></div>
        <div class="error_quote"><?php echo $ui_message; ?></div>
    </div>
    <div class="error_arrow"></div>
    <div class="error_attrib"> <span>What does the code say?</span>  
    </div>
    <div class="clear"></div>
</div>


</body></html>




