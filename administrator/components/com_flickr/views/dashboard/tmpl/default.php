<?php defined('KOOWA') or die('Restricted access'); ?>
<style>
div.container {
	overflow-x: auto;
}
.box {
	width: 300px;
	-moz-box-sizing: border-box;
    background-color: #F9F9F9;
    border-right: 1px solid #E1E1E1;
    margin: 3px 3px 3px 3px;
    border: 1px solid #E3E3E3;
    float: left;
}
ul.list {
    list-style: none outside none;
    margin: 0;
    overflow-y: auto;
    padding: 0;
    height: 480px;
}
ul.list li.first {
	border-top: none;
}
ul.list li{
	pading: 2px;
	border-bottom: 1px solid #E3E3E3;
    border-top: 1px solid #FFFFFF;
}
ul.list li p{
	margin: 0 0 0 80px;
}
img {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #BBBBBB;
    float: left;
    margin: 3px 10px 3px 0;
    padding: 4px;
}
</style>
<div class="container">
	<div id="interssingness" style="opacity: 0;" class="box">
		<h3><?= @text('Flickr: Interessingness'); ?></h3>
		<ul id="lazycontainer" class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.interestingness')->getList() as $photoIndex => $photo): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photo&id='.$photo->id); ?>"><?= @helper('image.photo', array('photo' => $photo->image,'size' => 's')) ?></a>
				<p><?= $photo->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div id="search" style="opacity: 0;" class="box">
		<h3><?= @text('Search: Nooku'); ?></h3>
		<ul class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.photos')->set('text','nooku')->search()->getList() as $photo): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photo&id='.$photo->id); ?>"><?= @helper('image.photo', array('photo' => $photo->image,'size' => 's')) ?></a>
				<p><?= $photo->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div id="photoset" style="opacity: 0;" class="box">
		<h3><?= @text('Photoset: Nooku Server'); ?></h3>
		<ul class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.photosets')->set('photoset_id','72157627021171180')->getPhotos()->getList() as $photo): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photo&id='.$photo->id); ?>"><?= @helper('image.photo', array('photo' => $photo->image,'size' => 's')) ?></a>
				<p><?= $photo->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div id="photosets" style="opacity: 0;" class="box">
		<h3><?= @text('Photosets: 39269070@N03'); ?></h3>
		<ul class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.photosets')->set('user_id','39269070@N03')->getList() as $photoIndex => $photoset): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photoset&id='.$photoset->id); ?>"><?= @helper('image.photo', array('photo' => $photoset->image,'size' => 's')) ?></a>
				<p><?= $photoset->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
<script type="text/javascript">
/*
---

script: More.js

name: More

description: MooTools More

license: MIT-style license

requires:
  - Core/MooTools

provides: [MooTools.More]

...
*/

MooTools.More = {
	'version': '1.2.5.1',
	'build': '254884f2b83651bf95260eed5c6cceb838e22d8e'
};


/*
---

script: Element.Measure.js

name: Element.Measure

description: Extends the Element native object to include methods useful in measuring dimensions.

credits: "Element.measure / .expose methods by Daniel Steigerwald License: MIT-style license. Copyright: Copyright (c) 2008 Daniel Steigerwald, daniel.steigerwald.cz"

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Element.Style
  - Core/Element.Dimensions
  - /MooTools.More

provides: [Element.Measure]

...
*/

Element.implement({

	measure: function(fn){
		var vis = function(el) {
			return !!(!el || el.offsetHeight || el.offsetWidth);
		};
		if (vis(this)) return fn.apply(this);
		var parent = this.getParent(),
			restorers = [],
			toMeasure = []; 
		while (!vis(parent) && parent != document.body) {
			toMeasure.push(parent.expose());
			parent = parent.getParent();
		}
		var restore = this.expose();
		var result = fn.apply(this);
		restore();
		toMeasure.each(function(restore){
			restore();
		});
		return result;
	},

	expose: function(){
		if (this.getStyle('display') != 'none') return $empty;
		var before = this.style.cssText;
		this.setStyles({
			display: 'block',
			position: 'absolute',
			visibility: 'hidden'
		});
		return function(){
			this.style.cssText = before;
		}.bind(this);
	},

	getDimensions: function(options){
		options = $merge({computeSize: false},options);
		var dim = {};
		var getSize = function(el, options){
			return (options.computeSize)?el.getComputedSize(options):el.getSize();
		};
		var parent = this.getParent('body');
		if (parent && this.getStyle('display') == 'none'){
			dim = this.measure(function(){
				return getSize(this, options);
			});
		} else if (parent){
			try { //safari sometimes crashes here, so catch it
				dim = getSize(this, options);
			}catch(e){}
		} else {
			dim = {x: 0, y: 0};
		}
		return $chk(dim.x) ? $extend(dim, {width: dim.x, height: dim.y}) : $extend(dim, {x: dim.width, y: dim.height});
	},

	getComputedSize: function(options){
		//legacy support for my stupid spelling error
		if (options && options.plains) options.planes = options.plains;
		
		options = $merge({
			styles: ['padding','border'],
			planes: {
				height: ['top','bottom'],
				width: ['left','right']
			},
			mode: 'both'
		}, options);
		
		var size = {width: 0,height: 0};
		switch (options.mode){
			case 'vertical':
				delete size.width;
				delete options.planes.width;
				break;
			case 'horizontal':
				delete size.height;
				delete options.planes.height;
				break;
		}
		var getStyles = [];
		//this function might be useful in other places; perhaps it should be outside this function?
		$each(options.planes, function(plane, key){
			plane.each(function(edge){
				options.styles.each(function(style){
					getStyles.push((style == 'border') ? style + '-' + edge + '-' + 'width' : style + '-' + edge);
				});
			});
		});
		var styles = {};
		getStyles.each(function(style){ styles[style] = this.getComputedStyle(style); }, this);
		var subtracted = [];
		$each(options.planes, function(plane, key){ //keys: width, height, planes: ['left', 'right'], ['top','bottom']
			var capitalized = key.capitalize();
			size['total' + capitalized] = size['computed' + capitalized] = 0;
			plane.each(function(edge){ //top, left, right, bottom
				size['computed' + edge.capitalize()] = 0;
				getStyles.each(function(style, i){ //padding, border, etc.
					//'padding-left'.test('left') size['totalWidth'] = size['width'] + [padding-left]
					if (style.test(edge)){
						styles[style] = styles[style].toInt() || 0; //styles['padding-left'] = 5;
						size['total' + capitalized] = size['total' + capitalized] + styles[style];
						size['computed' + edge.capitalize()] = size['computed' + edge.capitalize()] + styles[style];
					}
					//if width != width (so, padding-left, for instance), then subtract that from the total
					if (style.test(edge) && key != style &&
						(style.test('border') || style.test('padding')) && !subtracted.contains(style)){
						subtracted.push(style);
						size['computed' + capitalized] = size['computed' + capitalized]-styles[style];
					}
				});
			});
		});

		['Width', 'Height'].each(function(value){
			var lower = value.toLowerCase();
			if(!$chk(size[lower])) return;

			size[lower] = size[lower] + this['offset' + value] + size['computed' + value];
			size['total' + value] = size[lower] + size['total' + value];
			delete size['computed' + value];
		}, this);

		return $extend(styles, size);
	}

});

/*
---

script: Element.Shortcuts.js

name: Element.Shortcuts

description: Extends the Element native object to include some shortcut methods.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Element.Style
  - /MooTools.More

provides: [Element.Shortcuts]

...
*/

Element.implement({

	isDisplayed: function(){
		return this.getStyle('display') != 'none';
	},

	isVisible: function(){
		var w = this.offsetWidth,
			h = this.offsetHeight;
		return (w == 0 && h == 0) ? false : (w > 0 && h > 0) ? true : this.style.display != 'none';
	},

	toggle: function(){
		return this[this.isDisplayed() ? 'hide' : 'show']();
	},

	hide: function(){
		var d;
		try {
			//IE fails here if the element is not in the dom
			d = this.getStyle('display');
		} catch(e){}
		if (d == "none") return this;
		return this.store('element:_originalDisplay', d || '').setStyle('display', 'none');
	},

	show: function(display){
		if (!display && this.isDisplayed()) return this;
		display = display || this.retrieve('element:_originalDisplay') || 'block';
		return this.setStyle('display', (display == 'none') ? 'block' : display);
	},

	swapClass: function(remove, add){
		return this.removeClass(remove).addClass(add);
	}
});

Document.implement({
	clearSelection: function(){
		if (document.selection && document.selection.empty) {
			document.selection.empty();
		} else if (window.getSelection) {
			var selection = window.getSelection();
			if (selection && selection.removeAllRanges) selection.removeAllRanges();
		}
	}
});

/*
---

script: Fx.Reveal.js

name: Fx.Reveal

description: Defines Fx.Reveal, a class that shows and hides elements with a transition.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Fx.Morph
  - /Element.Shortcuts
  - /Element.Measure

provides: [Fx.Reveal]

...
*/

Fx.Reveal = new Class({

	Extends: Fx.Morph,

	options: {/*	  
		onShow: $empty(thisElement),
		onHide: $empty(thisElement),
		onComplete: $empty(thisElement),
		heightOverride: null,
		widthOverride: null, */
		link: 'cancel',
		styles: ['padding', 'border', 'margin'],
		transitionOpacity: !Browser.Engine.trident4,
		mode: 'vertical',
		display: function(){
			return this.element.get('tag') != 'tr' ? 'block' : 'table-row';
		},
		hideInputs: Browser.Engine.trident ? 'select, input, textarea, object, embed' : false,
		opacity: 1
	},

	dissolve: function(){
		try {
			if (!this.hiding && !this.showing){
				if (this.element.getStyle('display') != 'none'){
					this.hiding = true;
					this.showing = false;
					this.hidden = true;
					this.cssText = this.element.style.cssText;
					var startStyles = this.element.getComputedSize({
						styles: this.options.styles,
						mode: this.options.mode
					});
					this.element.setStyle('display', $lambda(this.options.display).apply(this));
					if (this.options.transitionOpacity) startStyles.opacity = this.options.opacity;
					var zero = {};
					$each(startStyles, function(style, name){
						zero[name] = [style, 0];
					}, this);
					this.element.setStyle('overflow', 'hidden');
					var hideThese = this.options.hideInputs ? this.element.getElements(this.options.hideInputs) : null;
					this.$chain.unshift(function(){
						if (this.hidden){
							this.hiding = false;
							$each(startStyles, function(style, name){
								startStyles[name] = style;
							}, this);
							this.element.style.cssText = this.cssText;
							this.element.setStyle('display', 'none');
							if (hideThese) hideThese.setStyle('visibility', 'visible');
						}
						this.fireEvent('hide', this.element);
						this.callChain();
					}.bind(this));
					if (hideThese) hideThese.setStyle('visibility', 'hidden');
					this.start(zero);
				} else {
					this.callChain.delay(10, this);
					this.fireEvent('complete', this.element);
					this.fireEvent('hide', this.element);
				}
			} else if (this.options.link == 'chain'){
				this.chain(this.dissolve.bind(this));
			} else if (this.options.link == 'cancel' && !this.hiding){
				this.cancel();
				this.dissolve();
			}
		} catch(e){
			this.hiding = false;
			this.element.setStyle('display', 'none');
			this.callChain.delay(10, this);
			this.fireEvent('complete', this.element);
			this.fireEvent('hide', this.element);
		}
		return this;
	},

	reveal: function(){
		try {
			if (!this.showing && !this.hiding){
				if (this.element.getStyle('display') == 'none'){
					this.showing = true;
					this.hiding = this.hidden =  false;
					var startStyles;
					this.cssText = this.element.style.cssText;
					//toggle display, but hide it
					this.element.measure(function(){
						//create the styles for the opened/visible state
						startStyles = this.element.getComputedSize({
							styles: this.options.styles,
							mode: this.options.mode
						});
					}.bind(this));
					$each(startStyles, function(style, name){
						startStyles[name] = style;
					});
					//if we're overridding height/width
					if ($chk(this.options.heightOverride)) startStyles.height = this.options.heightOverride.toInt();
					if ($chk(this.options.widthOverride)) startStyles.width = this.options.widthOverride.toInt();
					if (this.options.transitionOpacity) {
						this.element.setStyle('opacity', 0);
						startStyles.opacity = this.options.opacity;
					}
					//create the zero state for the beginning of the transition
					var zero = {
						height: 0,
						display: $lambda(this.options.display).apply(this)
					};
					$each(startStyles, function(style, name){ zero[name] = 0; });
					//set to zero
					this.element.setStyles($merge(zero, {overflow: 'hidden'}));
					//hide inputs
					var hideThese = this.options.hideInputs ? this.element.getElements(this.options.hideInputs) : null;
					if (hideThese) hideThese.setStyle('visibility', 'hidden');
					//start the effect
					this.start(startStyles);
					this.$chain.unshift(function(){
						this.element.style.cssText = this.cssText;
						this.element.setStyle('display', $lambda(this.options.display).apply(this));
						if (!this.hidden) this.showing = false;
						if (hideThese) hideThese.setStyle('visibility', 'visible');
						this.callChain();
						this.fireEvent('show', this.element);
					}.bind(this));
				} else {
					this.callChain();
					this.fireEvent('complete', this.element);
					this.fireEvent('show', this.element);
				}
			} else if (this.options.link == 'chain'){
				this.chain(this.reveal.bind(this));
			} else if (this.options.link == 'cancel' && !this.showing){
				this.cancel();
				this.reveal();
			}
		} catch(e){
			this.element.setStyles({
				display: $lambda(this.options.display).apply(this),
				visiblity: 'visible',
				opacity: this.options.opacity
			});
			this.showing = false;
			this.callChain.delay(10, this);
			this.fireEvent('complete', this.element);
			this.fireEvent('show', this.element);
		}
		return this;
	},

	toggle: function(){
		if (this.element.getStyle('display') == 'none'){
			this.reveal();
		} else {
			this.dissolve();
		}
		return this;
	},

	cancel: function(){
		this.parent.apply(this, arguments);
		this.element.style.cssText = this.cssText;
		this.hiding = false;
		this.showing = false;
		return this;
	}

});

Element.Properties.reveal = {

	set: function(options){
		var reveal = this.retrieve('reveal');
		if (reveal) reveal.cancel();
		return this.eliminate('reveal').store('reveal:options', options);
	},

	get: function(options){
		if (options || !this.retrieve('reveal')){
			if (options || !this.retrieve('reveal:options')) this.set('reveal', options);
			this.store('reveal', new Fx.Reveal(this, this.retrieve('reveal:options')));
		}
		return this.retrieve('reveal');
	}

};

Element.Properties.dissolve = Element.Properties.reveal;

Element.implement({

	reveal: function(options){
		this.get('reveal', options).reveal();
		return this;
	},

	dissolve: function(options){
		this.get('reveal', options).dissolve();
		return this;
	},

	nix: function(){
		var params = Array.link(arguments, {destroy: Boolean.type, options: Object.type});
		this.get('reveal', params.options).dissolve().chain(function(){
			this[params.destroy ? 'destroy' : 'dispose']();
		}.bind(this));
		return this;
	},

	wink: function(){
		var params = Array.link(arguments, {duration: Number.type, options: Object.type});
		var reveal = this.get('reveal', params.options);
		reveal.reveal().chain(function(){
			(function(){
				reveal.dissolve();
			}).delay(params.duration || 2000);
		});
	}


});

var LazyLoad = new Class({

	  Implements: [Options,Events],

	  /* additional options */
	  options: {
	    range: 200,
	    elements: "img",
	    container: window,
	    mode: "vertical",
	    realSrcAttribute: "data-src",
	    useFade: true
	  },

	  /* initialize */
	  initialize: function(options) {
	    
	    // Set the class options
	    this.setOptions(options);
	    
	    // Elementize items passed in
	    this.container = document.id(this.options.container);
	    this.elements = document.id(this.container == window ? document.body : this.container).getElements(this.options.elements);
	    
	    // Set a variable for the "highest" value this has been
	    this.largestPosition = 0;
	    
	    // Figure out which axis to check out
	    var axis = (this.options.mode == "vertical" ? "y": "x");
	    
	    // Calculate the offset
	    var offset = (this.container != window && this.container != document.body ? this.container : "");

	    //auto preset realSrcAttribute
	    var that = this;
	    this.elements.each(function(el) {
			el.set(that.options.realSrcAttribute,el.src);
		});

	    // Find elements remember and hold on to
	    this.elements = this.elements.filter(function(el) {
	      // Make opacity 0 if fadeIn should be done
	      if(this.options.useFade) el.setStyle("opacity",0);
	      // Get the image position
	      var elPos = el.getPosition(offset)[axis];
	      // If the element position is within range, load it
	      if(elPos < this.container.getSize()[axis] + this.options.range) {
	        this.loadImage(el);
	        return false;
	      }
	      return true;
	    },this);
	    
	    // Create the action function that will run on each scroll until all images are loaded
	    var action = function(e) {
	      
	      // Get the current position
	      var cpos = this.container.getScroll()[axis];
	      
	      // If the current position is higher than the last highest
	      if(cpos > this.largestPosition) {
	        
	        // Filter elements again
	        this.elements = this.elements.filter(function(el) {
	          
	          // If the element is within range...
	          if((cpos + this.options.range + this.container.getSize()[axis]) >= el.getPosition(offset)[axis]) {
	            
	            // Load the image!
	            this.loadImage(el);
	            return false;
	          }
	          return true;
	          
	        },this);
	        
	        // Update the "highest" position
	        this.largestPosition = cpos;
	      }
	      
	      // relay the class" scroll event
	      this.fireEvent("scroll");
	      
	      // If there are no elements left, remove the action event and fire complete
	      if(!this.elements.length) {
	        this.container.removeEvent("scroll",action);
	        this.fireEvent("complete");
	      }
	      
	    }.bind(this);
	    
	    // Add scroll listener
	    this.container.addEvent("scroll",action);
	  },
	  loadImage: function(image) {
	    // Set load event for fadeIn
	    if(this.options.useFade) {
	      image.addEvent("load",function(){
	        image.fade(1);
	      });
	    }
	    // Set the SRC
	    image.set("src",image.get(this.options.realSrcAttribute));
	    // Fire the image load event
	    this.fireEvent("load",[image]);
	  }
	});
</script>
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('div.box').each(function(box,index){
		$(box.id).fade('hide');
		new LazyLoad({container: $(box.id).getElement('ul')});
		timerFade = (index + 1) * 900;
		setTimeout(function(){
			$(box.id).fade('in');
			$(box.id).getElements('li').each(function(li,index){
				li.addEvents({
					mouseover: function(){
						this.tween('background-color','#F9F9F9','#f0f0f0 ');
					},
					mouseout: function(){
						setTimeout(this.tween('background-color','#f0f0f0 ','#F9F9F9'),3000)
					}
				});
				li.setStyle('margin-left',-300);
				timerLi = (index + 1) * 200;
				setTimeout(function(){li.tween('margin-left', -200, 0)},timerLi);
			});
		},timerFade);
	});
});
</script>