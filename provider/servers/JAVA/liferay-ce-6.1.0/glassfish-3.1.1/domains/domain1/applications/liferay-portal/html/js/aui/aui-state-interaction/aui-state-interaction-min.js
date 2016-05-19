AUI.add("aui-state-interaction",function(e){var h=e.Lang,d=h.isBoolean,c=h.isString,f=e.getClassName,i="state",j=f(i,"default"),g=f(i,"hover"),b=f(i,"active");var a=e.Component.create({NAME:"stateinteraction",NS:"StateInteraction",ATTRS:{active:{value:false},activeState:{value:true,validator:d},bubbleTarget:{value:null},classNames:{value:{}},"default":{value:false},defaultState:{value:true,validator:d},hover:{value:false},hoverState:{value:true,validator:d},node:{value:null}},EXTENDS:e.Plugin.Base,constructor:function(k){var m=k.host;var l=m;if(e.Widget&&m instanceof e.Widget){l=m.get("contentBox");}k.node=l;a.superclass.constructor.apply(this,arguments);},prototype:{initializer:function(){var k=this;var m=k.get("classNames.active");var l=k.get("classNames.default");var n=k.get("classNames.hover");k._CSS_STATES={active:c(m)?m:b,"default":c(l)?l:j,hover:c(n)?n:g};if(k.get("defaultState")){k.get("node").addClass(k._CSS_STATES["default"]);}k._createEvents();k._attachInteractionEvents();},_attachInteractionEvents:function(){var k=this;var l=k.get("node");l.on("click",k._fireEvents,k);l.on("mouseenter",e.rbind(k._fireEvents,k,"mouseover"));l.on("mouseleave",e.rbind(k._fireEvents,k,"mouseout"));k.after("activeChange",k._uiSetState);k.after("hoverChange",k._uiSetState);k.after("defaultChange",k._uiSetState);},_fireEvents:function(n,m){var k=this;var l=k.get("bubbleTarget");m=m||n.type;if(l){l.fire(m);}return k.fire(m);},_createEvents:function(){var k=this;var l=k.get("bubbleTarget");if(l){k.addTarget(l);}k.publish("click",{defaultFn:k._defClickFn,emitFacade:true});k.publish("mouseout",{defaultFn:k._defMouseOutFn,emitFacade:true});k.publish("mouseover",{defaultFn:k._defMouseOverFn,emitFacade:true});},_defClickFn:function(l){var k=this;k.set("active",!k.get("active"));},_defMouseOutFn:function(){var k=this;k.set("hover",false);},_defMouseOverFn:function(){var k=this;k.set("hover",true);},_uiSetState:function(m){var k=this;var l=m.attrName;if(k.get(l+"State")){var n="addClass";if(!m.newVal){n="removeClass";}k.get("node")[n](k._CSS_STATES[l]);}}}});e.namespace("Plugin").StateInteraction=a;},"1.0.1",{skinnable:false,requires:["aui-base","plugin"]});