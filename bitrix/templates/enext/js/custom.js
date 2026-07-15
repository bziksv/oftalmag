/***CUSTOM JAVASCRIPT FOR YOUR SITE***/

$(function(){
	
	$("[show-content]").each(function(i, el){
		let self = $(el);
		self.text(self.attr("show-content"));
	});

	$("[data-replace-content]").each(function(i, el){
		let self = $(el);
		self.replaceWith('<a>' + self.html() +'</a>')
	});

	$("[data-href-content]").each(function(i, el){
		let self = $(el);
		self.parents('a').attr("href", self.attr("href-content"))
	});

});
    $('#location_btn').click(function(){
        $('#location').bPopup({
            zIndex:1000,
            position: ['auto', 50]
        });
    });

    $('.city .item-city a.c').click(function(){
        var city = $(this).text();
        document.cookie = "city=" + city + "; path=/;";
		$('#location_btn').html('<i class="icon-map-marker"></i>' + '<span>' + city + '</span>');

        var bPopup = $('#location').bPopup();
        bPopup.close();
    });