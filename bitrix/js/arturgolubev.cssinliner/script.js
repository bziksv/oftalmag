var aginlinecss_fired = false;
function aginlinecss_get_scripts(url){
	BX.ajax({
		url: url,
		data: {},
		method: 'POST',
		dataType: 'html',
		timeout: 5,
		async: true,
		processData: true,
		scriptsRunFirst: false,
		start: true,
		cache: false,
		onsuccess: function(data){
			document.body.append(data);
		},
		onfailure: function(){
			console.log('arturgolubev.inlinecss js error');
		}
	});
}

function aginlinecss_action_scripts(getfile){
	if(BX.message('SITE_ID')){
		BX.onCustomEvent("agciAction", []);
		// if(getfile){
			// aginlinecss_get_scripts('/bitrix/tools/arturgolubev.cssinliner/' + BX.message('SITE_ID') + '/actionjs.php');
		// }
	}
}

function agic_ready_event(getfile){
	if(BX.message('SITE_ID')){
		BX.ready(function(){			
			BX.onCustomEvent("agciReady", []);
			// if(getfile){
				// aginlinecss_get_scripts('/bitrix/tools/arturgolubev.cssinliner/' + BX.message('SITE_ID') + '/lazyjs.php');
			// }
		});
	}
}

function agic_action_event(getfile){
	BX.ready(function(){
		if(BX.message('SITE_ID')){
			window.addEventListener('touchstart', function(){
				if(aginlinecss_fired === false){
					aginlinecss_fired = true;
					setTimeout(function(){aginlinecss_action_scripts(getfile);}, 50);
				}
			});
			window.addEventListener('scroll', function(){
				if(aginlinecss_fired === false){
					aginlinecss_fired = true;
					setTimeout(function(){aginlinecss_action_scripts(getfile);}, 50);
				}
			});
			window.addEventListener('mousemove', function(){
				if(aginlinecss_fired === false){
					aginlinecss_fired = true;
					setTimeout(function(){aginlinecss_action_scripts(getfile);}, 50);
				}
			});
		}
	});
}

function agic_webp_check(attrs){
	BX.ready(function(){
		let srcList = [], srcMake = [], imgs, i, bgsrc, src;
				
		imgs = document.querySelectorAll('img');
		for(i=0; i<imgs.length; i++){
			attrs.forEach(function(prop){
				src = imgs[i].getAttribute(prop);
				if(src){
					srcList.push(src);
				}
			});
		}
		
		imgs = document.querySelectorAll('*');
		for(i=0; i<imgs.length; i++){
			bgsrc = imgs[i].style.backgroundImage;
			if(bgsrc.length && bgsrc != 'none' && bgsrc != 'initial'){
				bgsrc = bgsrc.match(/url\(["']?([^"']*)["']?\)/);
				if(bgsrc){
					srcList.push(bgsrc[1]);
				}
			}
		}
		
		if(srcList.length){
			for(let i=0; i<srcList.length; i++){
				src = srcList[i];
				if(src.indexOf('.webp') == -1 && src.substr(0, 2) != '//'){
					if(src.indexOf('.png') > 0 || src.indexOf('.jpeg') > 0 || src.indexOf('.jpg') > 0){
						srcMake.push(src);
					}
				}
			}
		}
		
		if(srcMake.length){
			// console.log('srcMake', srcMake);
			
			BX.ajax({
				url: '/bitrix/tools/arturgolubev.cssinliner/ajax.php',
				data: {
					action: 'convert_webp',
					images: srcMake,
					path: window.location.pathname
				},
				method: 'POST',
				// dataType: 'jsoon',
				dataType: 'html',
				timeout: 10,
				async: true,
				processData: false,
				scriptsRunFirst: false,
				start: true,
				cache: false,
				onsuccess: function(data){
					// console.log(data);
				},
				onfailure: function(){
					console.log('arturgolubev.inlinecss webp error');
				}
			});
		}
	});
}

BX.addCustomEvent("onFrameDataReceivedBefore" , function(json) {
	for (i=0; i<json.dynamicBlocks.length; i++) {
		json.dynamicBlocks[i].PROPS.CSS = '';
	}
});

BX.addCustomEvent("onFrameDataReceived" , function(json) {
	if (document.body.classList.contains('agic_reload_css')) {		
		// console.log("Need reload css - agic_reload_css");
		
		var unitedStyle = false;
	
		if(json.css){
			for (key in json.css) {
				if(json.css[key].indexOf("_united_") >= 0){
					unitedStyle = json.css[key];
				}
			}
		}
		
		if(unitedStyle){
			// console.log('unitedStyle', unitedStyle);
			
			var link  = document.createElement('link');
			link.rel  = 'stylesheet';
			link.type = 'text/css';
			link.href = unitedStyle + '?v=1';
			link.media = 'all';
			document.head.appendChild(link);
		}
	}
});