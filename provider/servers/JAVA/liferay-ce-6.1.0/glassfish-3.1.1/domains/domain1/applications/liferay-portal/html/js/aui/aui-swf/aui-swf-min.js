AUI.add("aui-swf",function(k){var b=k.Lang,i=k.UA,g=k.getClassName,d="swf",o="10.22",j="http://fpdownload.macromedia.com/pub/flashplayer/update/current/swf/autoUpdater.swf?"+(+new Date),f="application/x-shockwave-flash",n="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000",r="YUI.AUI.SWF.eventHandler",p="ShockwaveFlash",q=0,v=YUI.AUI.namespace("SWF.instances"),m=g(d);YUI.AUI.SWF.eventHandler=function(w,e){v[w]._eventHandler(e);};if(i.gecko||i.webkit||i.opera){var c=navigator.mimeTypes[f];if(c){var l=c.enabledPlugin;var h=[];h=l.description.replace(/\s[rd]/g,".");h=h.replace(/[A-Za-z\s]+/g,"");h=h.split(".");q=h[0]+".";switch((h[2].toString()).length){case 1:q+="00";break;case 2:q+="0";break;}q+=h[2];q=parseFloat(q);}}else{if(i.ie){try{var t=new ActiveXObject(p+"."+"6");t.AllowScriptAccess="always";}catch(u){if(t!=null){q=6;}}if(q==0){try{var s=new ActiveXObject(p+"."+p);var h=[];h=s.GetVariable("$version");h=h.replace(/[A-Za-z\s]+/g,"");h=h.split(",");q=h[0]+".";switch((h[2].toString()).length){case 1:q+="00";break;case 2:q+="0";break;}}catch(u){}}}}i.flash=q;var a=k.Component.create({NAME:d,ATTRS:{url:{value:""},version:{value:q},useExpressInstall:{value:false},fixedAttributes:{value:{}},flashVars:{setter:"_setFlashVars",value:{}},render:{value:true}},constructor:function(y){var e=this;if(arguments.length>1){var x=arguments[0];var w=arguments[1];var z=arguments[2]||{};y={boundingBox:x,url:w,fixedAttributes:z.fixedAttributes,flashVars:z.flashVars};}a.superclass.constructor.call(this,y);},getFlashVersion:function(){return q;},isFlashVersionAtLeast:function(e){return q>=e;},prototype:{CONTENT_TEMPLATE:null,renderUI:function(){var G=this;var y=a.isFlashVersionAtLeast(G.get("version"));var D=(i.flash>=8);var x=D&&!y&&G.get("useExpressInstall");var B=G.get("url");if(x){B=j;}var z=k.guid();v[z]=this;G._swfId=z;var E=G.get("contentBox");var C=G.get("flashVars");k.mix(C,{YUISwfId:z,YUIBridgeCallback:r});var e=k.QueryString.stringify(C);var w="<object ";if((y||x)&&B){w+='id="'+z+'" ';if(i.ie){w+='classid="'+n+'" ';}else{w+='type="'+f+'" data="'+B+'" ';}w+='height="100%" width="100%">';if(i.ie){w+='<param name="movie" value="'+B+'"/>';}var F=G.get("fixedAttributes");for(var A in F){w+='<param name="'+A+'" value="'+F[A]+'" />';}if(e){w+='<param name="flashVars" value="'+e+'" />';}w+="</object>";E.set("innerHTML",w);}G._swf=k.one("#"+z);},bindUI:function(){var e=this;e.publish("swfReady",{fireOnce:true});},callSWF:function(y,w){var e=this;w=w||[];var x=e._swf.getDOM();if(x[y]){return x[y].apply(x,w);}return null;},toString:function(){var e=this;return"SWF"+e._swfId;},_eventHandler:function(x){var e=this;var w=x.type.replace(/Event$/,"");if(w!="log"){e.fire(w,x);}},_setFlashVars:function(w){var e=this;if(b.isString(w)){w=k.QueryString.parse(w);}return w;}}});k.SWF=a;},"1.0.1",{skinnable:false,requires:["aui-base","querystring-parse-simple","querystring-stringify-simple"]});