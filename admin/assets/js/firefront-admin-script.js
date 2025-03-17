/*
 * Zozo woo base addon sctipts
 */ 

(function( $ ) {

	"use strict";
	
	$( document ).ready(function() {		
		
		$("a.custom-sidebar-create").on( "click", function(){
			$(this).prev("form").submit();
		});
		
		$(".custom-sidebar-export").on("click", function() {
			$.ajax({
				type: "post",
				url: ajaxurl,
				data: "action=firefront-custom-sidebar-export&nonce="+ $("#firefront_custom_sidebar_nonce").val() ,
				success: function( data ){
					$("<a />", {
						"download": "custom-sidebars.json",
						"href" : "data:application/json," + encodeURIComponent( data )
					}).appendTo("body").on( "click", function() {
						$(this).remove();
					})[0].click ();
				}
			});
			return false;
		});
		
		$(".firefront-custom-sidebar-table a.firefront-sidebar-remove").on( "click", function(){
			$("#firefront-sidebar-remove-name").val( $(this).data("sidebar") );
			$(this).parents("form").submit();
		});
		
		$(".firefront-custom-font-table a.firefront-font-remove").on( "click", function(){
			$("#firefront-font-remove-name").val( $(this).data("font") );
			$(this).parents("form").submit();
		});
		
		//Modified
		$(".firefront-custom-fonts-upload").on( "click", function() {
			if( $('#firefront-custom-fonts').get(0).files.length ) {
				$(this).prev("form").submit();
			}
			return false;
		});
		
        $(".bulk-activator").on( "click", function() {
			$("#multi-plugins-active-form").find("input.firefront-bulk-plugins").remove();
			$( document ).find(".bulk-activator").each(function(){
				if( $(this).is(":checked") ){
					$("#multi-plugins-active-form").append('<input type="hidden" class="firefront-bulk-plugins" name="firefront_bulk_plugins['+ $(this).val() +']" value="'+ $(this).val() +'" />');
				}
			});
		});
		
		$(".firefront-bulk-action").on("click", function(e) {
			e.preventDefault();
			if( $( document ).find(".firefront-bulk-plugins").length ){
				$("#multi-plugins-active-form").submit();
			}else{
                alert("!You have to choose at least 1 plugin to make bulk action.");
			}
		});
		
		$("#multi-plugins-active-form").on( "submit", function(e) {
			e.preventDefault();
			var form_data = $("#multi-plugins-active-form").serializeArray();
			var form_data_n = {};
			$.each( form_data, function( key, value ) {
				form_data_n[value.name] = value.value;
			});
			form_data_n.plugins = firefront_admin_ajax_var.tgm_plugins;
			$(document).find(".firefront-plugins-box").addClass("overlay");
			$(document).find("p.firefront-settings-msg > img.bulk-process-loader").fadeIn(200);
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: form_data_n,
				success: function(data){
					window.location = location.href;
					$(document).find(".firefront-plugins-box").removeClass("overlay");
					$(document).find("p.firefront-settings-msg > img.bulk-process-loader").fadeOut(200);
				},
				error: function(response, errorThrown){
					window.location = location.href;
					$(document).find(".firefront-plugins-box").removeClass("overlay");
					$(document).find("p.firefront-settings-msg > img.bulk-process-loader").fadeOut(200);
				}
			});
		});
		
		if( $("#zozo-envato-deactivation-form").length ){
			$("#zozo-envato-deactivation-form").on( "submit", function(e) {
				e.preventDefault();
				var _form = $("#zozo-envato-deactivation-form");
				//enable loader
                _form.find('input[type="submit"]').attr("disabled", "disabled");
                _form.find(".process-loader").addClass("active");

                var form_data = _form.serialize();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: "action=firefront_theme_deactivate&" + form_data,
                    success: function(data) {
                        _form.find('input[type="submit"]').removeAttr("disabled");
                        _form.find(".process-loader").removeClass("active");
                        window.location = location.href;
                    },
                    error: function(response, errorThrown) {
                        window.location = location.href;
                    }
                });
            });
        }

        // Envato registration form submission
        if ($("#zozo-envato-registration-form").length) {
            $("#zozo-envato-registration-form").on("submit", function(e) {
                e.preventDefault();
                var _form = $("#zozo-envato-registration-form");
                _form.find('input[type="submit"]').attr("disabled", "disabled");
                _form.find(".process-loader").addClass("active");
                _form.find(".verfication-txt").removeClass("active");

                var form_data = _form.serialize();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: "action=firefront_theme_verify&" + form_data,
                    success: function(data) {
                        _form.find('input[type="submit"]').removeAttr("disabled");
                        _form.find(".process-loader").removeClass("active");
                        if (data.error_message) {
                            var errorMessage = data.error_message === 'already' ? firefront_admin_ajax_var.already_used : data.error_message;
                            _form.find(".verfication-txt").html(errorMessage).addClass("active");
                        } else if (data.status && data.status === 'success') {
                            window.location = location.href;
                        }
                    },
                    error: function(response, errorThrown) {
                        window.location = location.href;
                    }
                });
            });
        }

        // Bulk select all
        $(".bulk-select-all").on("change",  function() {
            var isChecked = $(this).is(":checked");
            $(".bulk-activator").prop("checked", isChecked);
            updateBulkPluginsInput();
        });

        // Update hidden inputs for selected plugins
        function updateBulkPluginsInput() {
            $("#multi-plugins-active-form").find("input.firefront-bulk-plugins").remove();
            $(".bulk-activator:checked").each(function() {
                var pluginSlug = $(this).val();
                $("#multi-plugins-active-form").append('<input type="hidden" class="firefront-bulk-plugins" name="firefront_bulk_plugins[' + pluginSlug + ']" value="' + pluginSlug + '" />');
            });
        }

		$(".bulk-select-all").on("change", function() {
			var isChecked = $(this).is(":checked");
			$(".bulk-activator").prop("checked", isChecked);
			updateBulkPluginsInput();
		});
	
		$(".bulk-activator").on("change", function() {
			updateBulkPluginsInput();
		});
	
		$(".firefront-bulk-action").on("click", function(e) {
			e.preventDefault();
			var action = $(".bulk-plugins-action-trigger").val();
			if (action) {
				$("#multi-plugins-active-form").submit();
			}
		});
	
		// Update hidden inputs for selected plugins
		function updateBulkPluginsInput() {
			$("#multi-plugins-active-form").find("input.firefront-bulk-plugins").remove();
			$(".bulk-activator:checked").each(function() {
				var pluginSlug = $(this).val();
				$("#multi-plugins-active-form").append('<input type="hidden" class="firefront-bulk-plugins" name="firefront_bulk_plugins[' + pluginSlug + ']" value="' + pluginSlug + '" />');
			});
		}

    });

	// Consolidated window load function
	$(window).on("load", function() {
		if ($(".admin-box-slide-wrap .owl-carousel").length) {
			$(".admin-box-slide-wrap .owl-carousel").owlCarousel({
				loop: true,
				margin: 0,
				autoplay: true,
				autoplayTimeout: 4000,
				items: 1
			});
		}
	});
	
})( jQuery );