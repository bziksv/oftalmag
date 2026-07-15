if(typeof jQuery !== 'undefined') {
	jQuery.event.special.touchstart = {
		setup: function( _, ns, handle ) {
			this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
		}
	};
	jQuery.event.special.touchmove = {
		setup: function( _, ns, handle ) {
			this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
		}
	};
	jQuery.event.special.wheel = {
		setup: function( _, ns, handle ){
			this.addEventListener("wheel", handle, { passive: true });
		}
	};
	jQuery.event.special.mousewheel = {
		setup: function( _, ns, handle ){
			this.addEventListener("mousewheel", handle, { passive: true });
		}
	};
}

if(typeof BX !== 'undefined') {
	function agci_addPassive(){
		var baseBind = BX.bind;
		
		BX.bind = function(t, e, h, o){
			if(e == 'touchstart' || e == 'touchmove' || e == 'wheel' || e == 'mousewheel'){	
				// console.log(e);
				if (!o)
					o = {};
				
				o['passive'] = true;
			}
			
			baseBind.call(this, t, e, h, o);
		};
	}
	
	agci_addPassive();
}