var agci_stop_optimize = 0, agci_global_next = false;

function agci_optimize_request(lastID){
	if(agci_stop_optimize) return 0;
	
	BX.ajax({
		url: '/bitrix/admin/arturgolubev_cssinliner_image_optimize.php?action=optimize',
		method: 'POST',
		data: {last: lastID, full_count: full_image_count, progress: now_image_progress, now_optimized: now_optimized, now_skiped: now_skiped},
		dataType: 'json',
		async: true,
		onsuccess: function (data) { 
			now_image_progress = now_image_progress + data.cnt;
			now_optimized = now_optimized + data.optimize;
			now_skiped = now_skiped + data.skip;
			
			$('.js-agci-optimize-table').html(data.progress);
			
			// console.log(data.optimize, data.rec.ORIGINAL_NAME);
			// if(!!data.optimize_faled){
				// console.log(data.rec);
			// }
						
			if(data.last){
				agci_optimize_request(data.last);
			}else{
				$('.js-agci-start-optimize, .js-agci-stop-optimize').hide();
			}
			
			agci_global_next = data.next;
			
			// console.log(data.next);
		},
		onfailure: function(arg, arg1, arg2){			
			var confirmMessage = BX.message('ARTURGOLUBEV_CSSINLINER_PHP_ERROR') + '<br/>';
			
			if(typeof agci_global_next == 'object'){
				confirmMessage = confirmMessage + BX.message('ARTURGOLUBEV_CSSINLINER_PHP_ERROR_IMAGE') + '<a href="'+agci_global_next.path+'" target="_blank">' + agci_global_next.path + '</a><br/>' + BX.message('ARTURGOLUBEV_CSSINLINER_PHP_ERROR_IMAGE_SIZE') + agci_global_next.size + BX.message('ARTURGOLUBEV_CSSINLINER_PHP_ERROR_IMAGE_SIZE_KB') + BX.message('ARTURGOLUBEV_CSSINLINER_PHP_ERROR_IMAGE_PROP') + agci_global_next.prop[0] + 'x' + agci_global_next.prop[1] + '<br/>';
				
				// console.log(agci_global_next);
			}
			
			if(typeof arg1.data == 'string'){
				confirmMessage = confirmMessage + arg1.data + '<br/>';
			}
			
			confirmMessage = confirmMessage + BX.message('ARTURGOLUBEV_CSSINLINER_PHP_ERROR_NOTE');
			
			agci_simple_confirm(BX.message('ARTURGOLUBEV_CSSINLINER_PHP_ERROR_TITLE'), confirmMessage, function(){
				agci_optimize_request(lastID);
			}, function(){
				$('.js-agci-stop-optimize').trigger('click');
			});
		}
		// error: function (jqXHR, textStatus, errorThrown) {
			// console.log('Error: ' + errorThrown);
			// console.log(this.data);
		// }
	});
}


function agci_simple_confirm(title, data, onAgree, onDisagree){
	var agci_confirm = new BX.PopupWindow("agwb_simple_confirm", null, {
		overlay: {backgroundColor: 'black', opacity: '80' },
		draggable: false,
		zIndex: 1,
		closeIcon : false,
		closeByEsc : true,
		lightShadow : false,
		autoHide : false,
		className: "agci_simple_window",
		content: data,
		titleBar: title,
		offsetTop : 1,
		offsetLeft : 0,
		buttons: [
			new BX.PopupWindowButton({
				text: BX.message("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_ONE_TRY"),
				className: "webform-button-link-cancel",
				events: {click: function(){
					this.popupWindow.close();
					this.popupWindow.destroy();
					onAgree();
				}}
			}),
			new BX.PopupWindowButton({
				text: BX.message("ARTURGOLUBEV_CSSINLINER_JS_CANCEL"),
				className: "webform-button-link-cancel",
				events: {click: function(){
					this.popupWindow.close();
					this.popupWindow.destroy();
					onDisagree();
				}}
			})
		]
	});
	agci_confirm.show();
}

$(document).ready(function(){
	$('.js-agci-start-optimize').click(function(){
		var $t = $(this), atext = $t.data('atext');
		
		agci_stop_optimize = 0;
		
		$t.html(atext).hide();
		
		$('.js-agci-stop-optimize').css('display', 'inline-block');
		$('.js-agci-optimize-table').css('display', 'block');
		
		agci_optimize_request(now_image_progress);
	});
	
	$('.js-agci-stop-optimize').click(function(){
		var $t = $(this);
		
		agci_stop_optimize = 1;
		
		$(this).hide();
		$('.js-agci-start-optimize').css('display', 'inline-block');
	});
});