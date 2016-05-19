AUI.add("aui-editor-creole-plugin",function(c){var g=c.Lang,h=g.isArray,b=g.isString,d=c.getClassName,k="creoleplugin",a="creole",f=/<(\/?)strong>/gi,j=/<(\/?)em>/gi,e="</span>";if(!YUI.AUI.defaults.EditorToolbar){YUI.AUI.defaults.EditorToolbar={STRINGS:{}};}var i=c.Component.create({NAME:k,NS:a,EXTENDS:c.Plugin.Base,ATTRS:{interwiki:{value:{WikiCreole:"http://www.wikicreole.org/wiki/",Wikipedia:"http://en.wikipedia.org/wiki/"}},host:{value:false},linkFormat:{value:""},strict:{value:false}},prototype:{html2creole:null,initializer:function(){var l=this;this._creole=new c.CreoleParser({forIE:c.UA.os==="windows",interwiki:this.get("interwiki"),linkFormat:this.get("linkFormat"),strict:this.get("strict")});var m=l.get("host");l.afterHostMethod("getContent",l.getCreoleCode,l);m.on("contentChange",l._contentChange,l);},_convertHTML2Creole:function(m){var l=this;if(!l.html2creole){l.html2creole=new c.HTML2CreoleConvertor({data:m});}else{l.html2creole.set("data",m);}return l.html2creole.convert();},getCreoleCode:function(){var l=this;var m=c.Do.originalRetVal;m=l._convertHTML2Creole(m);return new c.Do.AlterReturn(null,m);},getContentAsHtml:function(){var l=this;var m=l.get("host");return m.constructor.prototype.getContent.apply(m,arguments);},setContentAsCreoleCode:function(m){var l=this;var n=l.get("host");n.set("content",m);},_contentChange:function(m){var l=this;m.newVal=l._parseCreoleCode(m.newVal);m.stopImmediatePropagation();},_normalizeParsedData:function(m){var l=this;if(c.UA.gecko){m=l._normalizeParsedDataGecko(m);}else{if(c.UA.webkit){m=l._normalizeParsedDataWebKit(m);}}return m;},_normalizeParsedDataGecko:function(l){l=l.replace(f,function(p,o,n,m){if(!o){return'<span style="font-weight:bold;">';}else{return e;}});l=l.replace(j,function(p,o,n,m){if(!o){return'<span style="font-style:italic;">';}else{return e;}});return l;},_normalizeParsedDataWebKit:function(l){l=l.replace(f,"<$1b>");l=l.replace(j,"<$1i>");return l;},_parseCreoleCode:function(m){var l=this;var o=c.config.doc.createElement("div");l._creole.parse(o,m);var n=o.innerHTML;n=l._normalizeParsedData(n);return n;}}});c.namespace("Plugin").EditorCreoleCode=i;},"1.0.1",{requires:["aui-base","editor-base","aui-editor-html-creole","aui-editor-creole-parser"]});