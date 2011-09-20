var FlickrAnimation = new Class({
	
	Implements: [Options],
	
	options: {
		elements: '.column'
	},
	
	initialize: function()
	{
		if ($$(this.options.elements).lenght == 0) return;
		
		var that = this;
		
		$$('div.pagetitle').fade('hide');
		
		$$(this.options.elements).each(function(box,boxIndex){
			box.fade('hide');
			new LazyLoad({container: box.getElement('ul')});
			timerFade = (boxIndex + 1) * 900;
			setTimeout(function(){
				box.fade('in');
				box.getElements('li').each(function(li,index){
					if(index < 10)
					{
						li.setStyle('margin-left',-300);
						timerLi = (index + 1) * 200;
						setTimeout(function(){
							li.tween('margin-left', -200, 0);
							li.addEvent('click',function(){
								that.hideDashboard(boxIndex,index);
							});
						},timerLi);
					}
				});
			},timerFade);
		});
		
		setTimeout(function(){
			$$('div.pagetitle').fade('in');
		},$$(this.options.elements).length * 900);
	},

	hideDashboard: function(boxIndex,liIndex)
	{
		var that = this;
		var boxes = $$(this.options.elements);
		
		if(boxIndex == 0)
		{
			boxes.getLast().fade('out');
			setTimeout(function(){
				boxes[3].fade('out');
				setTimeout(function(){
					boxes[2].fade('out');
					setTimeout(function(){
						boxes[1].fade('out');
						setTimeout(function(){
							that.currentBoxAnimation(boxIndex,liIndex);
						},200);
					},200);
				},200);
			},200);
		}
		else {
			boxes.each(function(box,index){
				if(index != boxIndex)
				{
					boxTimer = (index - boxIndex) * 200;
					setTimeout(function(){
						boxes[index].fade('out');
					},boxTimer);
				}
				else {
					setTimeout(function(){
						that.currentBoxAnimation(boxIndex,liIndex);
					},200);
				}
			});
		}
	},

	currentBoxAnimation: function(boxIndex,liIndex)
	{
		var that = this;
		var boxes = $$(this.options.elements);
		var lis = boxes[boxIndex].getElements('li');
		
		this.options.currentIndex = boxIndex;
		this.options.currentBox = boxes[boxIndex];
		
		boxes[boxIndex].getElement('ul').setStyle('overflow-y','hidden');
		countLi = 0;
		boxes[boxIndex].getElements('li').each(function(li,index){
			if(index != liIndex)
			{
				setTimeout(function(){
					li.tween('margin-top',0,-li.height).fade('out');
				},countLi * 50);
				countLi++;
			}
		});
		setTimeout(function(){
			if(boxIndex > 0)
			{
				slidePosition = (boxIndex * 306) * -1;
				$('flickrdashboard').tween('margin-left',0,slidePosition);
			}
			setTimeout(function(){
				boxes[boxIndex].getElement('h3').fade('out');
				
				setTimeout(function(){
					href = lis[liIndex].getElement('a').getProperty('href');
					lis[liIndex].fade('out');
					
					boxes[boxIndex].getElement('ul').addClass('dn');
					new Fx.Morph(boxes[boxIndex], {
					    duration: 'long',
					    transition: Fx.Transitions.Sine.easeOut
					}).start('.column_info');
					
					that.loadItem(href);
				},200);
			},200);
		},200);
	},
	
	loadItem: function(url)
	{
		var that = this;
		
		if (!$('toolbar-cancel'))
		{
			TrElement = new Element('tr');
			TdElement = new Element('td',{id: 'toolbar-cancel',class: 'button'});
			TdLink = new Element('a',{class: 'toolbar',html: 'Dashboard'});
			
			TdLink.addEvent('click',function(){
				$$('table.toolbar').getLast().tween('margin-left',10,'-200');
				that.backDashboard();
			});
			
			spanElement = new Element('span',{class: 'icon-32-cancel'});
			spanElement.inject(TdLink);
			TdLink.inject(TdElement);
			TdElement.inject(TrElement);
			
			TrElement.inject($$('table.toolbar').getLast());
		}
		$$('table.toolbar').setStyle('margin-left','-200').setStyle('display','block').tween('margin-left','-200',10);
	},
	
	backDashboard: function()
	{
		var that = this;
		var boxes = $$(this.options.elements);
		
		new Fx.Morph(this.options.currentBox, {
		    duration: 'long',
		    transition: Fx.Transitions.Sine.easeOut
		}).start('.column');
		
		setTimeout(function(){
			that.options.currentBox.getElement('ul').removeClass('dn');
			that.options.currentBox.getElement('ul').setStyle('overflow-y','scroll');
			that.options.currentBox.getElement('h3').fade('in');
			
			countLi = 0;
			
			that.options.currentBox.getElements('li').each(function(li,index){
				setTimeout(function(){
					li.tween('margin-top',-li.height,0).fade('in');
				},countLi * 50);
				countLi++;
			});
			
			setTimeout(function(){
				if(that.options.currentIndex > 0)
				{
					slidePosition = $('flickrdashboard').getStyle('margin-left').replace('px','');
					$('flickrdashboard').tween('margin-left',slidePosition,0);
				}
				
				$$(that.options.elements).each(function(box,boxIndex){
					if(boxIndex != that.options.currentIndex)
					{
						timerFade = (boxIndex + 1) * 600;
						setTimeout(function(){
							box.fade('in');
							box.getElements('li').each(function(li,index){
								if(index < 10)
								{
									li.setStyle('margin-left',-300);
									timerLi = (index + 1) * 200;
									setTimeout(function(){
										li.tween('margin-left', -200, 0);
										li.addEvent('click',function(){
											that.hideDashboard(boxIndex,index);
										});
									},timerLi);
								}
							});
						},timerFade);
					}
				});
				
			},800);
		},500);
		
		
	}
});

window.addEvent('domready', function() {
	new FlickrAnimation();
});