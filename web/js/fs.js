

String.prototype.supplant = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        });
};


/* JSON support for old browsers */
/* also see  https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/JSON  */

if (!window.JSON) {
    console.log("Old browser using imitation of native JSON object");
    window.JSON = {
        parse: function (sJSON) {return eval("(" + sJSON + ")");},
        stringify: function (vContent) {
            if (vContent instanceof Object) {
                var sOutput = "";
                if (vContent.constructor === Array) {
                    for (var nId = 0; nId < vContent.length; sOutput += this.stringify(vContent[nId]) + ",", nId++);
                    return "[" + sOutput.substr(0, sOutput.length - 1) + "]";
                }

                if (vContent.toString !== Object.prototype.toString) {
                    return "\"" + vContent.toString().replace(/"/g, "\\$&") + "\"";
                }
                for (var sProp in vContent) {
                    sOutput += "\"" + sProp.replace(/"/g, "\\$&") + "\":" + this.stringify(vContent[sProp]) + ",";
                }
                return "{" + sOutput.substr(0, sOutput.length - 1) + "}";
          }
          return typeof vContent === "string" ? "\"" + vContent.replace(/"/g, "\\$&") + "\"" : String(vContent);
        }
  };
}


/* + namepsaces */
webgloo = window.webgloo || {};
webgloo.fs = webgloo.fs || {};

webgloo.fs.message = {
    get : function(key,data) {
        var buffer = '' ;
        if(webgloo.fs.message.hasOwnProperty(key)) {
            buffer = webgloo.fs.message[key].supplant(data);
        }

        return buffer ;
    }
} 

webgloo.fs.message.SPINNER = '<div> <img src="/css/asset/fs/fb_loader.gif" alt="spinner"/></div>' ;


webgloo.fs.Ajax = {
	 
	addSpinner : function(messageDivId) {
        $(messageDivId).html('');
        var content = webgloo.fs.message.SPINNER ;
        $(messageDivId).html(content);
       
    },

    show: function (messageDivId,content) {
    	$(messageDivId).html(content);
    },

    post:function (dataObj,options) {

        var defaults = {
            type : "POST",
            dataType : "json",
            timeout : 9000
        }

        var settings = $.extend({}, defaults, options);
        this.addSpinner(settings.messageDivId);
        

        $.ajax({
            url: dataObj.endPoint,
            type: settings.type ,
            dataType: settings.dataType,
            data :  dataObj.params,
            timeout: settings.timeout,
            processData:true,
           
            error: function(XMLHttpRequest, response){
                webgloo.fs.Ajax.show(settings.messageDivId,response);
            },

            // server script errors are 
            // reported inside success callback
            success: function(response){
            	webgloo.fs.Ajax.show(settings.messageDivId,response.message);
            }
        }); 
    }


}