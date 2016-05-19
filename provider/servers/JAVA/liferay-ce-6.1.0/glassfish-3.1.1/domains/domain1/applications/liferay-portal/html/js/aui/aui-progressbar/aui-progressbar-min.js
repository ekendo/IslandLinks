AUI.add("aui-progressbar",function(l){var g=l.Lang,N=g.isNumber,j=g.isString,m="",q=".",a=" ",v="auto",H="boundingBox",n="complete",s="contentBox",x="height",k="horizontal",J="label",D="lineHeight",K="max",p="min",z="offsetHeight",i="orientation",t="progress-bar",O="px",C="ratio",h="status",M="statusNode",P="step",G="text",y="textNode",d="useARIA",E="value",o="vertical",b="width",B=function(A){return parseFloat(A)||0;},c=l.getClassName,u=c(t,k),f=c(t,h),e=c(t,G),r=c(t,o),I='<div class="'+f+'"></div>',F='<div class="'+e+'"></div>';var w=l.Component.create({NAME:t,ATTRS:{useARIA:{value:true},height:{valueFn:function(){return this.get(H).get(z)||25;}},label:{value:m},max:{validator:N,value:100},min:{validator:N,value:0},orientation:{value:k,validator:function(A){return j(A)&&(A===k||A===o);}},ratio:{getter:"_getRatio",readOnly:true},step:{getter:"_getStep",readOnly:true},statusNode:{valueFn:function(){return l.Node.create(I);}},textNode:{valueFn:function(){return l.Node.create(F);}},value:{setter:B,validator:function(A){return N(B(A))&&((A>=this.get(p))&&(A<=this.get(K)));},value:0}},HTML_PARSER:{label:function(A){var L=A.one(q+e);if(L){return L.html();}},statusNode:q+f,textNode:q+e},UI_ATTRS:[J,i,E],prototype:{renderUI:function(){var A=this;A._renderStatusNode();A._renderTextNode();},syncUI:function(){var A=this;if(A.get(d)){A.plug(l.Plugin.Aria,{attributes:{value:"valuenow",max:"valuemax",min:"valuemin",orientation:"orientation",label:"label"}});}},_getContentBoxSize:function(){var A=this;var L=A.get(s);return B(L.getStyle(this.get(i)===k?b:x));},_getPixelStep:function(){var A=this;return A._getContentBoxSize()*A.get(C);},_getRatio:function(){var A=this;var L=A.get(p);var Q=(A.get(E)-L)/(A.get(K)-L);return Math.max(Q,0);},_getStep:function(){return this.get(C)*100;},_renderStatusNode:function(){var A=this;A.get(s).append(A.get(M));},_renderTextNode:function(){var A=this;A.get(s).append(A.get(y));},_uiSetLabel:function(A){this.get(y).html(A);},_uiSetOrientation:function(R){var A=this;var Q=A.get(H);var L=(R===k);Q.toggleClass(u,L);Q.toggleClass(r,!L);A._uiSizeTextNode();},_uiSetValue:function(S){var A=this;var Q=A.get(M);var L=A._getPixelStep();var R={};if(A.get(i)===k){R={height:"100%",top:v,width:L+O};}else{R={height:L+O,top:B(A._getContentBoxSize()-L)+O,width:"100%"};}if(A.get(P)>=100){A.fire(n);}Q.setStyles(R);},_uiSizeTextNode:function(){var A=this;var L=A.get(s);var Q=A.get(y);Q.setStyle(D,L.getStyle(x));}}});l.ProgressBar=w;},"1.0.1",{requires:["aui-base","aui-aria"],skinnable:true});