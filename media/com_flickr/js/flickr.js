var FlickrAnimation = new Class({
	initialize: function()
	{
		var that = this;
		
		$$('div.box').each(function(box,boxIndex){
			box.fade('hide');
			new LazyLoad({container: box.getElement('ul')});
			timerFade = (boxIndex + 1) * 900;
			setTimeout(function(){
				box.fade('in');
				box.getElements('li').each(function(li,index){
					li.setStyle('margin-left',-300);
					timerLi = (index + 1) * 200;
					setTimeout(function(){
						li.tween('margin-left', -200, 0);
						li.addEvent('click',function(){
							that.hideDashboard(boxIndex,index);
						});
					},timerLi);
				});
			},timerFade);
		});
	},

	hideDashboard: function(boxIndex,liIndex)
	{
		var that = this;
		var boxes = $$('div.box');
		
		if(boxIndex == 0)
		{
			$$('div.box').getLast().fade('out');
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
			$$('div.box').each(function(box,index){
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
		var boxes = $$('div.box');
		var lis = boxes[boxIndex].getElements('li');
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

			var item = new Element('div',{id: 'item'});
			var Info = new Element('div').addClassname('box').setStyle('width','200px');
			
			//boxes[boxIndex].getElement('ul').tween('width',480,200);
			setTimeout(function(){
				boxes[boxIndex].getElement('h3').tween('margin-top',0,'-30');
				setTimeout(function(){
					lis[liIndex].fade('out');
					
					//boxes[boxIndex].fade('out');
					
					
					//lis[liIndex].tween('width',lis[liIndex].getStyle('width').replace('px',''),window.innerWidth).tween('height',lis[liIndex].getStyle('height').replace('px',''),window.innerHeight);
					
				},200);
			},200);
		},200);
	}
});