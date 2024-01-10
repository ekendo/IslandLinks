/*
moo.fx pack, effects extensions for moo.fx.
by Valerio Proietti (http://mad4milk.net) MIT-style LICENSE
for more info (http://moofx.mad4milk.net).
10/25/2005
v1.0
*/

//text size modify, works with ems only
fx.Text = Class.create();
Object.extend(Object.extend(fx.Text.prototype, fx.Layout.prototype), {	
	increase: function() {
		this.el.style.fontSize = this.now + "em";
	}
});

//composition effect, calls Width and Height alltogheter
fx.Resize = Class.create();
fx.Resize.prototype = {
	initialize: function(el, options) {
		this.h = new fx.Height(el, options); 
		options.onComplete = null;
		this.w = new fx.Width(el, options);
		this.el = $(el);
	},

	toggle: function(){
		this.h.toggle();
		this.w.toggle();
	},

	modify: function(hto, wto) {
		this.h.custom(this.el.offsetHeight, this.el.offsetHeight + hto);
		this.w.custom(this.el.offsetWidth, this.el.offsetWidth + wto);
	},

	custom: function(hto, wto) {
		this.h.custom(this.el.offsetHeight, hto);
		this.w.custom(this.el.offsetWidth, wto);
	},

	hide: function(){
		this.h.hide();
		this.w.hide();
	}
}

//composition effect, calls Opacity and (Width and/or Height) alltogheter
fx.FadeSize = Class.create();
fx.FadeSize.prototype = {
	initialize: function(el, options) {
		this.el = $(el);
		this.el.o = new fx.Opacity(el, options);
		this.el.h = new fx.Height(el, options);
		this.el.w = new fx.Width(el, options);
		options.onComplete = null;
	},

	toggle: function() {
		this.el.o.toggle();
		for (var i = 0; i < arguments.length; i++) {
			if (arguments[i] == 'height') this.el.h.toggle();
			if (arguments[i] == 'width') this.el.w.toggle();
		}
	},

	hide: function(){
		this.el.o.hide();
		for (var i = 0; i < arguments.length; i++) {
			if (arguments[i] == 'height') this.el.h.hide();
			if (arguments[i] == 'width') this.el.w.hide();
		}
	}
}

//intended to work with arrays.
var Multi = new Object();
Multi = function(){};
Multi.prototype = {
	initialize: function(elements, options){
		this.options = options;
		this.el = this.getElementsFromArray(elements);
		for (i=0;i<this.el.length;i++){
			this.effect(this.el[i]);
		}
	},

	getElementsFromArray: function(array) {
		var elements = new Array();
		for (i=0;i<array.length;i++) { 
			elements.push($(array[i])); 
		}
		return elements;
	}
}

//Fadesize with arrays
fx.MultiFadeSize = Class.create();
fx.MultiFadeSize.prototype = Object.extend(new Multi(), {
	effect: function(el){
		el.fs = new fx.FadeSize(el, this.options);
	},

	showThisHideOpen: function(el, delay, mode){
		for (i=0;i<this.el.length;i++){
			if (this.el[i].offsetHeight > 0 && this.el[i] != el && this.el[i].h.timer == null && el.h.timer == null){
				this.el[i].fs.toggle(mode);
				setTimeout(function(){el.fs.toggle(mode);}.bind(el), delay);
			}
			
		}
	},

	hide: function(el, mode){
		el.fs.hide(mode);
	}
});