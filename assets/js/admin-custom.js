	;(function($){
	$("#usrole").change(function(){ 
		var vals = $("#usrole").val();
		var pathname = window.location.pathname;
        var url      = window.location.href;
		window.location.assign(url+"&role="+vals);
	});
		$("#oxo_admin_options > li").click(function(){ 
			$(this).addClass("active").siblings("li").removeClass("active");
		});

	})(jQuery);