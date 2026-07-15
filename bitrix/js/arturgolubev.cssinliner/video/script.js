if(window.frameCacheVars !== undefined && typeof BX == 'function'){
	BX.addCustomEvent("onFrameDataReceived", function(json){
		agll_rutube_init();
		agll_youtube_init();
		agll_videoframe_resize();
	});
}else{
	document.addEventListener("DOMContentLoaded", function(event) { 
		agll_rutube_init();
		agll_youtube_init();
		agll_videoframe_resize();
	});
}

function agll_rutube_init(){
	var youtube = document.querySelectorAll('.js-rt-ll');
	
	 for(var i=0; i<youtube.length; i++){
		if(youtube[i].classList.contains('ag-yt-ll-inited'))
			continue;

        var preview = youtube[i].dataset.preview,
            yt_image_div = document.createElement('div'),
            yt_playbtn = document.createElement('div');
		
        yt_image_div.classList.add('ag-yt-ll-img');
		youtube[i].appendChild(yt_image_div);
		yt_image_div.style.backgroundImage = "url('" + preview  + "')";

        yt_playbtn.classList.add('ag-yt-ll-playbtn');
        youtube[i].appendChild(yt_playbtn);
        
        youtube[i].querySelector('.ag-yt-ll-playbtn').addEventListener('click',function(){
			// console.log('PLAY RUTUBE!');
			
			var yt_container = this.parentElement, new_frame = document.createElement('iframe');
			new_frame.src = 'https://rutube.ru/play/embed/'  + yt_container.dataset.video + '?autoplay=1&ref=0';
			new_frame.setAttribute('frameBorder', '0');
			new_frame.setAttribute('allow', 'clipboard-write; autoplay');
			new_frame.setAttribute('id', 'rutube_'+yt_container.dataset.video);
			new_frame.setAttribute('allowfullscreen','');
			new_frame.setAttribute('webkitAllowFullScreen','');
			new_frame.setAttribute('mozallowfullscreen','');
			yt_container.appendChild(new_frame);
        });
		
		youtube[i].classList.add("ag-yt-ll-inited");
    }
	
	window.addEventListener('message', function (event) {
		if(event.origin == 'https://rutube.ru'){			
			try {
				var message = JSON.parse(event.data);				
				switch (message.type) {
					case 'player:ready':
						setTimeout(function(){
							var player = document.getElementById('rutube_'+message.data.videoId);
							player.contentWindow.postMessage(JSON.stringify({
								type: 'player:play',
								data: {}
							}), '*');
							player.contentWindow.postMessage(JSON.stringify({
								type: 'player:pause',
								data: {}
							}), '*');
						}, 1000);
					break;
				};
			}catch(e){
				
			}
		}
	});
}

function agll_youtube_init(){
	var youtube = document.querySelectorAll('.js-yt-ll');
	if(youtube.length && window.screen.width < 767){
		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	}
	
    for(var i=0; i<youtube.length; i++){
		if(youtube[i].classList.contains('ag-yt-ll-inited'))
			continue;

        var preview = youtube[i].dataset.preview,
            yt_image_div = document.createElement('div'),
            yt_playbtn = document.createElement('div');
		
        yt_image_div.classList.add('ag-yt-ll-img');
		youtube[i].appendChild(yt_image_div);
		yt_image_div.style.backgroundImage = "url('//img.youtube.com/vi/" + youtube[i].dataset.video  + "/"+preview+".jpg')";

        yt_playbtn.classList.add('ag-yt-ll-playbtn');
        youtube[i].appendChild(yt_playbtn);
        
        youtube[i].querySelector('.ag-yt-ll-playbtn').addEventListener('click',function(){
			if(typeof(YT) == 'object'){
				var yt_container = this.parentElement;
				yt_container.setAttribute('id', 'video-'+yt_container.dataset.video);
				new YT.Player('video-'+yt_container.dataset.video, {
					videoId: yt_container.dataset.video,
					playerVars: {
						autoplay: 1,
					},
					events: {
						onReady: function(e) {
							e.target.mute();
							e.target.playVideo();
						}
					}
				});
			}else{
				var yt_container = this.parentElement, new_frame = document.createElement('iframe');
				new_frame.src = 'https://www.' + yt_container.dataset.domain + '.com/embed/' + yt_container.dataset.video + '?autoplay=1';
				new_frame.setAttribute('allow','accelerometer;autoplay;encrypted-media;gyroscope;picture-in-picture');
				new_frame.setAttribute('allowfullscreen','');
				yt_container.appendChild(new_frame);
			}
        });
		
		youtube[i].classList.add("ag-yt-ll-inited");
    }
}

window.addEventListener('resize', function(event) {
	agll_videoframe_resize();
});
	
function agll_videoframe_resize(){
	var youtube = document.querySelectorAll('.ag-yt-ll');
    for(var i=0; i<youtube.length; i++){
		var fw = youtube[i].dataset.fw, fh = youtube[i].dataset.fh;
		
		if(fw && fh){
			if(youtube[i].offsetWidth > 0 && youtube[i].offsetWidth < fw){
				var nheight = youtube[i].offsetWidth / fw * fh;				
				youtube[i].style.height = nheight + 'px';
			}else{
				youtube[i].style.height = fh + 'px';
			}
		}
	}
}