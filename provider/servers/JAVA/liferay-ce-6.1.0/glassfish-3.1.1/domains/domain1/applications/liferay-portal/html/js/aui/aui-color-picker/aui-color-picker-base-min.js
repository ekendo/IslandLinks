AUI.add("aui-color-picker-base",function(r){var f=r.Lang,m=f.isArray,k=f.isObject,D=r.ColorUtil,g="colorpicker",i=r.getClassName,C=r.WidgetStdMod,h=i(g,"canvas"),E=i(g,"hue-canvas"),p=i(g,"container"),s=i(g,"controls"),l=i(g,"panel"),q=i(g,"swatch"),c=i(g,"swatch-current"),x=i(g,"swatch-original"),t=i(g,"thumb"),n=i(g,"thumb-image"),j=i(g,"hue-thumb"),d=i(g,"hue-thumb-image"),y=i(g,"hue","slider"),w=i(g,"hue","slider","content"),e=i(g,"trigger"),z='<div class="'+h+'"></div>',v='<span class="'+E+'"></span>',o='<div class="'+q+'"></div>',F='<div class="'+c+'"></div>',b='<div class="'+x+'"></div>',u='<div class="'+t+'"><div class="'+n+'"></div></div>',B='<span class="'+j+'"><span class="'+d+'"></span></span>';var a=r.Component.create({NAME:g,ATTRS:{colors:{value:{},getter:function(){var A=this;var H=A.get("rgb");var I=A.get("hex");var G={};r.mix(G,H);G.hex=I;return G;}},hex:{value:"FFFFFF",getter:function(){var A=this;var G=A.get("rgb");var H=G.hex;if(H){H=H.split("#").join("");}else{H=D.rgb2hex(G);}return H;},setter:function(H){var A=this;if(H){var G=D.getRGB("#"+H);H=G.hex.split("#").join("");A.set("rgb",G);}else{H=r.Attribute.INVALID_VALUE;}return H;}},hideOn:{value:"click"},hsv:{getter:function(H){var A=this;var G=A.get("rgb");return D.rgb2hsv(G);},setter:function(H){var A=this;if(m(H)){var I=A.get("hsv");var G=D.hsv2rgb(H);A.set("rgb",G);I={hue:H[0],saturation:H[1],value:[2]};}else{if(!k(H)){H=r.Attribute.INVALID_VALUE;}}return H;},value:{h:0,s:0,v:0}},showOn:{value:"click"},pickersize:{value:180},rgb:{value:new D.RGB(255,255,255),setter:function(J){var G=this;var I;var H;var A;var K=true;if(m(J)){I=J[0];H=J[0];A=J[0];}else{if(k){I=J.r;H=J.g;A=J.b;}else{J=r.Attribute.INVALID_VALUE;K=false;}}if(K){I=D.constrainTo(I,0,255,255);H=D.constrainTo(H,0,255,255);A=D.constrainTo(A,0,255,255);J=new D.RGB(I,H,A);}return J;}},strings:{value:{R:"R",G:"G",B:"B",H:"H",S:"S",V:"V",HEX:"#",DEG:"\u00B0",PERCENT:"%"}},triggerParent:{value:null},trigger:{lazyAdd:true,getter:function(G){var A=this;if(!G){A._buttonTrigger=new r.ButtonItem({cssClass:e,icon:"pencil"});G=A._buttonTrigger.get("boundingBox");G=new r.NodeList(G);A.set("trigger",G);}return G;}}},EXTENDS:r.OverlayContext,prototype:{renderUI:function(){var A=this;var H=A._buttonTrigger;if(H&&!H.get("rendered")){var G=A.get("triggerParent");if(!G){G=A.get("boundingBox").get("parentNode");}H.render(G);}A._renderContainer();A._renderSliders();A._renderControls();},bindUI:function(){var A=this;a.superclass.bindUI.apply(this,arguments);A._createEvents();A._colorCanvas.on("mousedown",A._onCanvasMouseDown,A);A._colorPicker.on("drag:start",A._onThumbDragStart,A);A._colorPicker.after("drag:drag",A._afterThumbDrag,A);A._hueSlider.after("valueChange",A._afterValueChange,A);var G=A._colorForm.get("contentBox");G.delegate("change",r.bind(A._onFormChange,A),"input");A.after("hexChange",A._updateRGB);A.after("rgbChange",A._updateRGB);A._colorSwatchOriginal.on("click",A._restoreRGB,A);A.after("visibleChange",A._afterVisibleChangeCP);},syncUI:function(){var A=this;A._updatePickerOffset();var G=A.get("rgb");A._updateControls();A._updateOriginalRGB();},_afterThumbDrag:function(G){var A=this;var H=A._translateOffset(G.pageX,G.pageY);if(!A._preventDragEvent){A.fire("colorChange",{ddEvent:G});}A._canvasThumbXY=H;},_afterValueChange:function(G){var A=this;if(G.src!="controls"){A.fire("colorChange",{slideEvent:G});}},_afterVisibleChangeCP:function(G){var A=this;if(G.newVal){A.refreshAlign();A._hueSlider.syncUI();}A._updateOriginalRGB();},_convertOffsetToValue:function(G,I){var A=this;if(m(G)){return A._convertOffsetToValue.apply(A,G);}var H=A.get("pickersize");G=Math.round(((G*H/100)));I=Math.round((H-(I*H/100)));return[G,I];},_convertValueToOffset:function(G,H){var A=this;if(m(G)){return A._convertValueToOffset.apply(A,G);}G=Math.round(G+A._offsetXY[0]);H=Math.round(H+A._offsetXY[1]);return[G,H];},_createEvents:function(){var A=this;A.publish("colorChange",{defaultFn:A._onColorChange});},_getHuePicker:function(){var A=this;var H=A.get("pickersize");var G=(H-A._hueSlider.get("value"))/H;G=D.constrainTo(G,0,1,0);return(G===1)?0:G;},_getPickerSize:function(){var A=this;if(!A._pickerSize){var G=A._colorCanvas;var H=G.get("offsetWidth");if(!H){H=G.getComputedStyle("width");}H=parseInt(H,10);var I=A._pickerThumb.get("offsetWidth");H-=I;A._pickerSize=H;}return A._pickerSize;},_getSaturationPicker:function(){var A=this;return A._canvasThumbXY[0]/A._getPickerSize();},_getThumbOffset:function(){var G=this;if(!G._thumbOffset){var H=G._pickerThumb;var A=H.get("offsetHeight");var I=H.get("offsetWidth");G._thumbOffset=[Math.floor(I/2),Math.floor(A/2)];}return G._thumbOffset;},_getValuePicker:function(){var A=this;var G=A._getPickerSize();return((G-A._canvasThumbXY[1]))/G;},_onCanvasMouseDown:function(G){var A=this;A._setDragStart(G.pageX,G.pageY);G.halt();A.fire("colorChange",{ddEvent:G});},_onColorChange:function(J){var A=this;var G=A._getHuePicker();var I=A._getSaturationPicker();var K=A._getValuePicker();var H=D.hsv2rgb(G,I,K);if(J.src!="controls"){A.set("rgb",H);}A._updateControls();if(!J.ddEvent){if(!J.slideEvent){A._updateHue();A._updatePickerThumb();G=A._getHuePicker();}var L=D.hsv2rgb(G,1,1);A._updateCanvas(L);}A._updateColorSwatch();},_onFormChange:function(H){var A=this;var G=H.currentTarget;var I=G.get("id");if(I!="hex"){I="rgb."+I;}A.set(I,G.val());},_onThumbDragStart:function(G){var A=this;A._updatePickerOffset();},_renderContainer:function(){var A=this;if(!A._pickerContainer){var G=new r.Panel({cssClass:l,icons:[{icon:"close",id:"close",handler:{fn:A.hide,context:A}}]}).render(A.get("contentBox"));var H=G.bodyNode;H.addClass(p);A._pickerContainer=H;}},_renderControls:function(){var G=this;G._colorSwatch=r.Node.create(o);G._colorSwatchCurrent=r.Node.create(F);G._colorSwatchOriginal=r.Node.create(b);G._colorSwatch.appendChild(G._colorSwatchCurrent);G._colorSwatch.appendChild(G._colorSwatchOriginal);G._pickerContainer.appendChild(G._colorSwatch);var A=G.get("strings");var H=new r.Form({labelAlign:"left"}).render(G._pickerContainer);
H.add([{id:"r",labelText:A.R,size:3},{id:"g",labelText:A.G,size:3},{id:"b",labelText:A.B,size:3},{id:"hex",labelText:A.HEX,size:6}],true);H.get("boundingBox").addClass(s);G._colorForm=H;},_renderSliders:function(){var A=this;A._colorCanvas=r.Node.create(z);A._pickerThumb=r.Node.create(u);A._colorCanvas.appendChild(A._pickerThumb);A._pickerContainer.appendChild(A._colorCanvas);var G=A.get("pickersize");A._colorPicker=new r.DD.Drag({node:A._pickerThumb}).plug(r.Plugin.DDConstrained,{constrain2node:A._colorCanvas});var H=new r.Slider({axis:"y",min:0,max:G,length:A._colorCanvas.get("offsetHeight")});H.RAIL_TEMPLATE=v;H.THUMB_TEMPLATE=B;H.get("boundingBox").addClass(y);H.get("contentBox").addClass(w);H.render(A._pickerContainer);A._hueSlider=H;},_restoreRGB:function(G){var A=this;A.set("rgb",A._oldRGB);A._updateHue();A._updatePickerThumb();A._updateColorSwatch();A.fire("colorChange");},_setDragStart:function(H,K){var G=this;if(m(H)){return G._setDragStart.apply(G,H);}var A=G._colorPicker;A._dragThreshMet=true;A._fixIEMouseDown();r.DD.DDM.activeDrag=A;var J=A.get("dragNode").getXY();var I=G._getThumbOffset();J[0]+=I[0];J[1]+=I[1];A._setStartPosition(J);A.set("activeHandle",A.get("dragNode"));A.start();A._alignNode([H,K]);},_translateOffset:function(G,J){var A=this;var H=A._offsetXY;var I=[];I[0]=Math.round(G-H[0]);I[1]=Math.round(J-H[1]);return I;},_updateCanvas:function(G){var A=this;G=G||A.get("rgb");A._colorCanvas.setStyle("backgroundColor","rgb("+[G.r,G.g,G.b].join(",")+")");},_updateColorSwatch:function(G){var A=this;G=G||A.get("rgb");A._colorSwatchCurrent.setStyle("backgroundColor","rgb("+[G.r,G.g,G.b].join(",")+")");},_updateControls:function(){var A=this;var G=A.get("colors");A._colorForm.set("values",G);},_updateHue:function(){var A=this;var H=A.get("pickersize");var G=A.get("hsv.h");G=H-Math.round(G*H);if(G===H){G=0;}A._hueSlider.set("value",G,{src:"controls"});},_updateOriginalColorSwatch:function(G){var A=this;G=G||A.get("rgb");A._colorSwatchOriginal.setStyle("backgroundColor","rgb("+[G.r,G.g,G.b].join(",")+")");},_updateOriginalRGB:function(){var A=this;A._oldRGB=A.get("rgb");A._updateOriginalColorSwatch(A._oldRGB);},_updatePickerOffset:function(){var A=this;A._offsetXY=A._colorCanvas.getXY();},_updatePickerThumb:function(){var G=this;G._updatePickerOffset();var H=G.get("hsv");var I=G.get("pickersize");H.s=Math.round(H.s*100);var J=H.s;H.v=Math.round(H.v*100);var K=H.v;var L=G._convertOffsetToValue(J,K);L=G._convertValueToOffset(L);G._canvasThumbXY=L;var A=G._colorPicker;G._preventDragEvent=true;A._setStartPosition(A.get("dragNode").getXY());A._alignNode(L,true);G._preventDragEvent=false;},_updateRGB:function(G){var A=this;if(G.subAttrName||G.attrName=="hex"){A.fire("colorChange",{src:"controls"});}},_canvasThumbXY:[0,0],_offsetXY:[0,0]}});r.ColorPicker=a;},"1.0.1",{skinnable:true,requires:["aui-overlay-context","dd-drag","slider","aui-button-item","aui-color-util","aui-form-base","aui-panel"]});