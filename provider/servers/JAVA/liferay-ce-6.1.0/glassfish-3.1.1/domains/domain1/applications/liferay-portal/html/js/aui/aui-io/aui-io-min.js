AUI.add("aui-io-request",function(m){var f=m.Lang,C=f.isBoolean,o=f.isFunction,g=f.isString,E=YUI.AUI.namespace("defaults.io"),G=function(A){return function(){return E[A];};},u="active",b="arguments",v="autoLoad",r="cache",F="cfg",q="complete",M="content-type",w="context",l="data",e="dataType",i="",K="end",z="failure",a="form",s="get",j="headers",J="IORequest",d="json",x="method",t="responseData",y="start",k="success",B="sync",p="timeout",n="transaction",D="uri",I="xdr",N="xml",H="Parser error: IO dataType is not correctly parsing",c={all:"*/*",html:"text/html",json:"application/json, text/javascript",text:"text/plain",xml:"application/xml, text/xml"};var h=m.Component.create({NAME:J,ATTRS:{autoLoad:{value:true,validator:C},cache:{value:true,validator:C},dataType:{setter:function(A){return(A||i).toLowerCase();},value:null,validator:g},responseData:{setter:function(A){return this._setResponseData(A);},value:null},uri:{setter:function(A){return this._parseURL(A);},value:null,validator:g},active:{value:false,validator:C},cfg:{getter:function(){var A=this;return{arguments:A.get(b),context:A.get(w),data:A.getFormattedData(),form:A.get(a),headers:A.get(j),method:A.get(x),on:{complete:m.bind(A.fire,A,q),end:m.bind(A._end,A),failure:m.bind(A.fire,A,z),start:m.bind(A.fire,A,y),success:m.bind(A._success,A)},sync:A.get(B),timeout:A.get(p),xdr:A.get(I)};},readOnly:true},transaction:{value:null},arguments:{valueFn:G(b)},context:{valueFn:G(w)},data:{valueFn:G(l)},form:{valueFn:G(a)},headers:{getter:function(O){var P=[];var A=this;var L=A.get(e);if(L){P.push(c[L]);}P.push(c.all);return m.merge(O,{Accept:P.join(", ")});},valueFn:G(j)},method:{valueFn:G(x)},selector:{value:null},sync:{valueFn:G(B)},timeout:{valueFn:G(p)},xdr:{valueFn:G(I)}},EXTENDS:m.Plugin.Base,prototype:{init:function(L){var A=this;h.superclass.init.apply(this,arguments);A._autoStart();},destructor:function(){var A=this;A.stop();A.set(n,null);},getFormattedData:function(){var A=this;var O=A.get(l);var L=E.dataFormatter;if(o(L)){O=L.call(A,O);}return O;},start:function(){var A=this;A.destructor();A.set(u,true);var L=A._yuiIOObj;if(!L){L=new m.IO();A._yuiIOObj=L;}var O=L.send(A.get(D),A.get(F));A.set(n,O);},stop:function(){var A=this;var L=A.get(n);if(L){L.abort();}},_autoStart:function(){var A=this;if(A.get(v)){A.start();}},_parseURL:function(P){var A=this;var L=A.get(r);var S=A.get(x);if((L===false)&&(S==s)){var R=+new Date;var O=P.replace(/(\?|&)_=.*?(&|$)/,"$1_="+R+"$2");P=O+((O==P)?(P.match(/\?/)?"&":"?")+"_="+R:"");}var Q=E.uriFormatter;if(o(Q)){P=Q.apply(A,[P]);}return P;},_end:function(O,L){var A=this;A.set(u,false);A.set(n,null);A.fire(K,O,L);},_success:function(P,O,L){var A=this;A.set(t,O);A.fire(k,P,O,L);},_setResponseData:function(S){var Q=null;var L=this;if(S){var P=L.get(e);var T=S.getResponseHeader(M)||"";if((P==N)||(!P&&T.indexOf(N)>=0)){Q=S.responseXML;if(Q.documentElement.tagName=="parsererror"){throw H;}}else{Q=S.responseText;}if(Q===i){Q=null;}if(P==d){try{Q=m.JSON.parse(Q);}catch(R){}}else{var A=L.get("selector");if(Q&&A){var O;if(Q.documentElement){O=m.one(Q);}else{O=m.Node.create(Q);}Q=O.all(A);}}}return Q;}}});m.IORequest=h;m.io.request=function(L,A){return new m.IORequest(m.merge(A,{uri:L}));};},"1.0.1",{requires:["aui-base","io-base","json","plugin","querystring-stringify"]});AUI.add("aui-io-plugin",function(s){var n=s.Lang,o=n.isBoolean,p=n.isString,t=function(A){return(A instanceof s.Node);},u=s.WidgetStdMod,c="Node",l="Widget",E="",d="failure",g="failureMessage",w="host",h="icon",i="io",e="IOPlugin",v="loading",f="loadingMask",D="node",r="outer",z="parseContent",k="queue",b="rendered",m="section",C="showLoading",y="success",q="type",a="where",x=s.getClassName,j=x(h,v);var B=s.Component.create({NAME:e,NS:i,ATTRS:{node:{value:null,getter:function(H){var A=this;if(!H){var G=A.get(w);var F=A.get(q);if(F==c){H=G;}else{if(F==l){var I=A.get(m);if(!G.getStdModNode(I)){G.setStdModContent(I,E);}H=G.getStdModNode(I);}}}return s.one(H);},validator:t},failureMessage:{value:"Failed to retrieve content",validator:p},loadingMask:{value:{}},parseContent:{value:true,validator:o},showLoading:{value:true,validator:o},section:{value:u.BODY,validator:function(A){return(!A||A==u.BODY||A==u.HEADER||A==u.FOOTER);}},type:{readOnly:true,valueFn:function(){var A=this;var F=c;if(A.get(w) instanceof s.Widget){F=l;}return F;},validator:p},where:{value:u.REPLACE,validator:function(A){return(!A||A==u.AFTER||A==u.BEFORE||A==u.REPLACE||A==r);}}},EXTENDS:s.IORequest,prototype:{bindUI:function(){var A=this;A.on("activeChange",A._onActiveChange);A.on(y,A._successHandler);A.on(d,A._failureHandler);if((A.get(q)==l)&&A.get(C)){var F=A.get(w);F.after("heightChange",A._syncLoadingMaskUI,A);F.after("widthChange",A._syncLoadingMaskUI,A);}},_autoStart:function(){var A=this;A.bindUI();B.superclass._autoStart.apply(this,arguments);},_bindParseContent:function(){var A=this;var F=A.get(D);if(F&&!F.ParseContent&&A.get(z)){F.plug(s.Plugin.ParseContent);}},hideLoading:function(){var A=this;var F=A.get(D);if(F.loadingmask){F.loadingmask.hide();}},setContent:function(F){var A=this;A._bindParseContent();A._getContentSetterByType().apply(A,[F]);if(A.overlayMaskBoundingBox){A.overlayMaskBoundingBox.remove();}},showLoading:function(){var A=this;var F=A.get(D);if(F.loadingmask){if(A.overlayMaskBoundingBox){F.append(A.overlayMaskBoundingBox);}}else{F.plug(s.LoadingMask,A.get(f));A.overlayMaskBoundingBox=F.loadingmask.overlayMask.get("boundingBox");}F.loadingmask.show();},start:function(){var A=this;var F=A.get(w);if(!F.get(b)){F.after("render",function(){A._setLoadingUI(true);});}B.superclass.start.apply(A,arguments);},_getContentSetterByType:function(){var A=this;var F={Node:function(J){var G=this;var I=G.get(D);if(J instanceof s.NodeList){J=J.toFrag();}if(J instanceof s.Node){J=J._node;}var H=G.get(a);if(H==r){I.replace(J);}else{I.insert(J,H);}},Widget:function(I){var G=this;var H=G.get(w);H.setStdModContent.apply(H,[G.get(m),I,G.get(a)]);}};return F[this.get(q)];
},_setLoadingUI:function(F){var A=this;if(A.get(C)){if(F){A.showLoading();}else{A.hideLoading();}}},_syncLoadingMaskUI:function(){var A=this;A.get(D).loadingmask.refreshMask();},_successHandler:function(F,H,G){var A=this;A.setContent(this.get("responseData"));},_failureHandler:function(F,H,G){var A=this;A.setContent(A.get(g));},_onActiveChange:function(G){var A=this;var F=A.get(w);var H=A.get(q)==l;if(!H||(H&&F&&F.get(b))){A._setLoadingUI(G.newVal);}}}});s.Node.prototype.load=function(J,I,K){var F=this;var H=J.indexOf(" ");var A;if(H>0){A=J.slice(H,J.length);J=J.slice(0,H);}if(n.isFunction(I)){K=I;I=null;}I=I||{};if(K){I.after=I.after||{};I.after.success=K;}var G=I.where;I.uri=J;I.where=G;if(A){I.selector=A;I.where=G||"replace";}F.plug(s.Plugin.IO,I);return F;};s.namespace("Plugin").IO=B;},"1.0.1",{requires:["aui-overlay-base","aui-parse-content","aui-io-request","aui-loading-mask"]});AUI.add("aui-io",function(a){},"1.0.1",{use:["aui-io-request","aui-io-plugin"],skinnable:false});