AUI.add("aui-tabs-menu-plugin",function(m){var f=m.Lang,i=m.getClassName,d="tab",w="tabview",g="tabviewmenu",c="TabViewMenuPlugin",b="contentNode",v="host",j="listNode",a="rendered",r=i(d),h=i(w,"list"),s=i(w,"list","content"),o=i(g,"item"),p=i(g,"item","label"),n=i(g,"list"),k=i(g,"trigger"),u=i(w,"wrapper"),l="first",q="last",t="<ul></ul>",y='<li class="'+o+'" data-index="{0}"><a href="javascript:;" class="'+p+'">{1}</a></li>',e="<div></div>";var x=m.Component.create({NAME:c,NS:g,EXTENDS:m.Plugin.Base,prototype:{initializer:function(){var z=this;z.afterHostMethod("renderUI",z.renderUI);z.afterHostMethod("bindUI",z.bindUI);z.afterHostMethod("addTab",z.addTab);z.afterHostMethod("removeTab",z.removeTab);z.afterHostMethod("selectTab",z.selectTab);z.afterHostMethod("_onActiveTabChange",z._onActiveTabChange);z.afterHostMethod("_renderTabs",z._renderTabs);z._updateMenuTask=m.debounce(z._updateMenu,1,z);z._updateUITask=m.debounce(z._updateUI,1,z);},bindUI:function(){var z=this;var A=z.get(v);m.on("windowresize",z._onWindowResize,z);},renderUI:function(){var z=this;var B=z.get(v);var A=B.get(j);var C=z._wrapper;z._listNodeOuterWidth=(parseFloat(A.getComputedStyle("marginLeft"))+parseFloat(C.getComputedStyle("borderLeftWidth"))+parseFloat(A.getComputedStyle("paddingLeft"))+parseFloat(A.getComputedStyle("paddingRight"))+parseFloat(C.getComputedStyle("borderRightWidth"))+parseFloat(A.getComputedStyle("marginRight")));z._updateUITask();},addTab:function(B,A){var z=this;var C=z.get(v);if(C.get(a)){z._updateUITask();}},removeTab:function(A){var z=this;var B=z.get(v);if(B.get(a)){z._updateUITask();}},selectTab:function(A){var z=this;z._updateMenuTask();z.fire("selectTab",{index:A});},_hideMenu:function(){var z=this;var B=z.get(v);var A=B.get(j);A.all("."+r).show();if(z._menuOverlay){z._menuOverlay.hide();z._triggerNode.hide();}},_onActiveTabChange:function(A){var z=this;z._updateMenuTask();},_onWindowResize:function(B){var A=this;if(A._menuNode){var z=A.get(v).get(b);A._contentWidth=z.get("offsetWidth")-A._listNodeOuterWidth;A._updateMenuTask();}else{A._updateUITask();}},_renderMenu:function(){var z=this;var A=m.Node.create(e);var B=m.Node.create(t);A.addClass(k);z._wrapper.append(A);var D=new m.OverlayContext({align:{points:["tr","br"]},contentBox:B,cancellableHide:true,cssClass:n,hideDelay:1000,hideOn:"mouseout",showDelay:0,showOn:"click",trigger:A}).render();D.refreshAlign();z._menuNode=B;z._triggerNode=A;z._menuOverlay=D;z.after("selectTab",D.hide,D);var C=z.get(v);B.delegate("click",function(F){var E=F.currentTarget.get("parentNode").attr("data-index");C.selectTab(E);},"li a");},_renderTabs:function(){var A=this;var E=A.get(v);var z=E.get(b);var D=E.get(j);D.removeClass(h);D.addClass(s);var C=E._createDefaultContentContainer();C.addClass(h);var B=E._createDefaultContentContainer();B.addClass(u);B.append(C);z.insert(B,D);C.append(D);A._wrapper=B;A._content=C;},_updateMenu:function(){var N=this;var O=N.get(v);var I=N._menuNode;var C=N._wrapper;if(I){var M=true;var G=C.get("offsetWidth");var J=N._itemsWidth;if(J[J.length-1]>N._contentWidth){var H=O.get(j);var L=H.all("."+r);var F=O.getTabIndex(O.get("activeTab"));var E=(F!=0?J[F]-J[F-1]:0);var z=N._contentWidth;var K=O.selectTab;var D=[];var B=[];L.each(function(Q,P,T){var S=(P<F?E:0);if(P!=F&&J[P]+S>z){Q.hide();D[0]=P;D[1]=Q.get("text");var R=f.sub(y,D);B.push(R);M=false;}else{Q.show();}});I.setContent(B.join(""));var A=I.all("li");A.first().addClass(l);A.last().addClass(q);}if(M){N._hideMenu();}else{N._triggerNode.show();}}},_updateUI:function(){var A=this;var D=A.get(v);A._hideMenu();var z=D.get(b);var C=D.get(j);var B=C.all("."+r);A._contentWidth=z.get("offsetWidth")-A._listNodeOuterWidth;A._itemsWidth=[];var G=A._itemsWidth;var E=(parseFloat(C.getComputedStyle("paddingLeft"))+parseFloat(C.getComputedStyle("paddingRight")));var F=B.size()-1;B.each(function(I,H,K){var L=(parseFloat(I.getComputedStyle("marginRight"))+parseFloat(I.getComputedStyle("marginLeft")));var J=H-1;if(H>0){G[J]=E+L+I.get("offsetLeft");}if(H==F){G[H]=G[J]+I.get("offsetWidth");}});if(G[G.length-1]>A._contentWidth){if(!A._menuOverlay){A._renderMenu();}A._updateMenuTask();}}}});m.namespace("Plugin").TabViewMenu=x;},"1.0.1",{requires:["aui-component","aui-state-interaction","aui-tabs-base","aui-overlay-context","plugin"]});