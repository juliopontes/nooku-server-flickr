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
					}
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
		
		setTimeout(function(){
			$$('div.pagetitle').fade('in');
		},$$(this.options.elements).length * 900);
	},

	hideDashboard: function(boxIndex,liIndex)
	{
		var that = this;
		var boxes = $$(this.options.elements);
		
		href = boxes[boxIndex].getElement('li:index('+liIndex+')').getElement('a').getProperty('href');
		
		params = href.split("?")[1].split("&");
		params.each(function(param){
			argument = param.split('=');
			if ( argument[0] == 'view' ){
				that.options.view = argument[1];
			}
		});
		
		new Request.JSON({method: 'get',url: href, onSuccess: function(data){
			that.options.response = data;
			that.dispatch();
		}}).send('format=json');
		
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
	
	loadItem: function(href)
	{
		var that = this;
		
		$('flickrdashboard').addClass('dn');
		$('flickritem').removeClass('dn');
		
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
		
		setTimeout(function(){
			$('column_info').getElements('h3').each(function(element){
				element.fade('in');
			});
			
			$('column_info').getElements('.box').each(function(element){
				element.fade('in');
			});
			
			$('column_data').tween('margin-left',window.innerWidth,0);
		},500);
	},
	
	backDashboard: function()
	{
		var that = this;
		var boxes = $$(this.options.elements);
		
		$$('div.pagetitle').getLast().set('html', 'Dashboard');
		
		$('column_data').tween('margin-left',0,window.innerWidth);
		
		setTimeout(function(){
			$('flickritem').addClass('dn');
			$('flickrdashboard').removeClass('dn');
			
			new Fx.Morph(that.options.currentBox, {
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
									}
									timerLi = (index + 1) * 200;
									setTimeout(function(){
										li.tween('margin-left', -200, 0);
										li.addEvent('click',function(){
											that.hideDashboard(boxIndex,index);
										});
									},timerLi);
								});
							},timerFade);
						}
					});
					
				},800);
			},500);
		},300);
	},
	
	dispatch: function()
	{
		$$('div.pagetitle').getLast().set('html', this.options.response.title);
		
		switch (this.options.view)
		{
			case 'photo':
				this.item = new FlickrHtmlPhoto(this.options.response);
				break;
			case 'photoset':
				this.item = new FlickrHtmlPhotoset(this.options.response);
				break;
		}
	}
});

var FlickrHtmlDefault = new Class({
	
	addInfo: function(title,html)
	{
		h3 = new Element('h3').set('html',title).fade('hide');
		div = new Element('div').addClass('box').fade('hide');
		
		html.each(function(htmlc){
			htmlc.inject(div);
		});
		
		h3.inject($('column_info'));
		div.inject($('column_info'));
	},
	
	addData: function(html)
	{
		html.each(function(htmlc){
			htmlc.inject($('column_data'));
		});
	},
	
	createImage: function(size)
	{
		image = this.response.image;
		src = 'http://farm'+image.farm+'.static.flickr.com/'+image.server+'/'+image.id+'_'+image.secret;
		
		if (size != undefined)
		{
			src += '_'+size;
		}

		src += '.jpg';
		
		Asset.image(src);
		
		return src;
	},
	
	initialize: function(response)
	{
		this.response = response;
		
		$('column_data').empty().setStyle('margin-left',window.innerWidth);
		$('column_info').empty();
		
		this.createSource();
		this.createInfoData();
		this.createItemData();
	},
	
	createSource: function()
	{
		linkItem = new Element('a',{href: this.response.url,text: 'teste'});
		linkVia = new Element('a',{href: this.response.short_url,text: 'via (flickr.com)'});
		PItem = new Element('p');
		PVia = new Element('p');
		
		linkItem.inject(PItem);
		linkVia.inject(PVia);
		
		this.addInfo('Source', [PItem,PVia]);
	}
});

var FlickrHtmlPhoto = new Class({
	Extends: FlickrHtmlDefault,
	
	createItemData: function()
	{
		var that = this;
		html = new Array();
		
		html.include(new Element('img',{src: that.createImage()}));
		
		if (this.response.description != "")
			html.include(new Element('p',{html: this.response.description}));
		
		this.addData(html);
	},
	
	createInfoData: function()
	{
		this.createOwner();
		this.createTakenDate();
		this.createTags();
		this.createSizes();
	},
	
	createOwner: function()
	{
		html = new Array();
		if (this.response.owner.username != "")
			html.include(new Element('p',{text: this.response.owner.username}));
		
		if (this.response.owner.realname != "")
			html.include(new Element('p',{text: '('+this.response.owner.realname+')'}));
		
		if (this.response.owner.location != "")
			html.include(new Element('p',{text: this.response.owner.location}));
		
		this.addInfo('Owner', html);
	},
	
	createTakenDate: function()
	{
		html = new Array();
		if (this.response.owner.taken_date != "")
			html.include(new Element('p',{text: this.response.taken_date}))
			
		this.addInfo('Taken date', html);
	},
	
	createTags: function()
	{
		html = new Array();
		
		if ( this.response.tags.length > 0 )
		{
			this.response.tags.each(function(tag){
				html.include(new Element('span',{text: tag}).addClass('tag'));
			});
		}
			
		this.addInfo('Tags', html);
	},
	
	createSizes: function()
	{
		html = new Array();
		
		if ( this.response.sizes.length > 0 )
		{
			this.response.sizes.each(function(size){
				html.include(new Element('p',{text: size.width+' x '+size.height}));
			});
		}
			
		this.addInfo('Sizes', html);
	}
});

var FlickrHtmlPhotoset = new Class({
	Extends: FlickrHtmlDefault,
	
	createInfoData: function()
	{
		this.createOwner();
		this.createDescription();
	},
	
	createItemData: function()
	{
		var that = this;
		html = new Array();
		
		this.response.photos.each(function(photo){
			alert(photo);
			that.response.image = photo;
			html.include(new Element('img',{src: that.createImage()}));
			
			if (that.response.description != "")
				html.include(new Element('p',{html: that.response.description}));
		});
		
		this.addData(html);
	},
	
	createOwner: function()
	{
		html = new Array();
		if (this.response.owner != "")
			html.include(new Element('p',{text: this.response.owner}));
		
		this.addInfo('Owner', html);
	},
	
	createDescription: function()
	{
		html = new Array();
		if (this.response.description != "")
			html.include(new Element('p',{html: this.response.description}))
			
		this.addInfo('Description', html);
	}
});


window.addEvent('domready', function() {
	new FlickrAnimation();
});