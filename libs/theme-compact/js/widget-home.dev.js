(function(){

	$(".dragbox").each(function(){
		show_empty_container();

		$(this).hover(
			function(){
				$(this).find("a.colspace").addClass("collapse");
				$(this).find(".to-remove").css("visibility", "visible");
				$(this).find(".to-config").css("visibility", "visible");
			}, 		
			function(){
				$(this).find("a.colspace").removeClass("collapse");
				$(this).find(".to-remove").css("visibility", "hidden");
				$(this).find(".to-config").css("visibility", "hidden");
			}
		)
		.click()	
		.end()		
		
		.find("div.panel-heading > a.coltoggle").click(function(){
			//$(this).parent().siblings('.panel-body').slideToggle('fast');
			//$(this).toggleClass('down');
			
			//$(this).find("div.gd-header > span.configure").css("visibility", "hidden");
		})	
		.end()	
		.find(".configure").css("visibility", "hidden");
	});
	
	$(".column .meta-box-sortables").sortable({
		connectWith: ".column .meta-box-sortables",
		handle: "a.to-move",
		cursor: "move",
		opacity: 0.8,
		placeholder: "placeholder",
		forcePlaceholderSize: true,
		stop: function(event, ui){
			//$(ui.item).find("a.to-move").click();
			updateWidgetData();
			show_empty_container();
		}
		
	})
	.enableSelection();
	
	
	dialog = {
		defaults: {
			autohide: false,
			buttons: {
				'Close': function() {
					dialog.hide();
				}
			}
		},
		init: function() {
			$('body').append('<div id="modal" class="modal fade"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close modal-close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title"></h4></div><div class="modal-status"></div><div class="modal-body"></div></div></div></div>');
			$('.modal-close').click(dialog.hide);
		},
		show: function(options) {
			var options = $.extend({}, this.defaults, options);
			switch (options.type) {
				case 'ajax':
					$.ajax({
						type: 'GET',
						datatype: 'html',
						url: options.url,
						success: function(data) {
							options.content = data;
							dialog._show(options);
						}
					});
					break;
				default:
					this._show(options);
					break;
			}
		},
		_show: function(options) {
			$('.modal-footer').remove();
			//membuat tombol footer otomatis
			if (options.buttons) {
				var lx;
				var l = 0;
				$('.modal-content').append('<div class="modal-footer"></div>');
				$.each(options.buttons, function(k, v) {
					if( l == 0 ) lx = 'btn-primary';
					else lx = 'btn-default';
					
					yx = '';
					if( l != 0 ) yx = 'data-dismiss="modal"';
					
					$('<button class="btn '+lx+' '+k.toLowerCase()+'"'+yx+'></button>').text(k).click(v).appendTo('.modal-footer');
					l++;
				});
			}
			
			$("#modal").modal();
			$(".modal-dialog").css({width:''+options.width+'px'});
			$("h4.modal-title").html(options.title);
			$(".modal-body").html(options.content);
			if (options.autohide) {
				setTimeout(function() {dialog.hide();}, options.autohide);
			}
		},
		hide: function() {
			$('h4.modal-title').html('');
			$('#modal-body').html('');
			$('#modal-footer').remove();
		}
	};
	dialog.init();
	
	$(".panel-content").each(function() {
		var content = $(this);
		var url = base_url + $(this).attr('data-url')
		var loading = $.ajax({
			type: 'GET',
			datatype: 'html',
			url: url,
			beforeSend: function () {
				var ajax_loader = '<span class="text-muted">Memuat...</span>';
				content.html(ajax_loader);
			}
		});
		loading.fail(function ( data ) {
			content.html('<p id="error_no_ani">Request failed: '+data+'</p>');
		});
		loading.done(function ( data ) {
			content.html(data);
		});
	});
	$(".modal-show").click(function() {
		var button_footer = '';
		var url 	= $(this).attr('data-url');
		var type 	= $(this).attr('data-type');
		var w 	= $(this).attr('data-width');
		url = base_url + url;
		
		
		if( type == 'add' ){
			button_footer = {
				'Add': function() {
					ajax_feedback(url,'');
				},
				'Cancel': dialog.hide
			};
		}else if( type == 'edit' ){
			button_footer = {
				'Edit': function() {
					ajax_feedback(url,'');
				},
				'Cancel': dialog.hide
			};
		}else if( type == 'confirm' ){
			button_footer = {
				'Yes': function() {
					ajax_feedback(url,'');
				},
				'No': dialog.hide
			};
		}else if( type == 'send' ){
			button_footer = {
				'Send': function() {
					ajax_feedback(url);
				},
				'Cancel': dialog.hide
			};
		}else if( type == 'show' ){
			button_footer = '';
		}
		
		dialog.show({
			title: $(this).attr('data-original-title'),
			type: 'ajax', //ajax
			url: url,
			width: w,
			buttons: button_footer
		});
		return false;
	});
	
	
	function ajax_feedback(url, val ){
		
		if( $('#modal form').attr('action') ) 
			url = $('#modal form').attr('action');
		
		$.ajax({
			type: 'POST',
			url: url,
			data: $('#modal form').serialize() + val,
			error: function(data) {		
				console.log(data);		
				$('<span id="error"></span>')
				.text('Failed to execution this data, please check your ajax file.')
				.prependTo('.modal-status')
				.delay(1000)
				.fadeOut(1000, function(){
					//document.location.reload();
					$(this).remove();
				});
			},
			success: function(data) {				
				var fld = 0, msg = '',cls = 'error';
				
				if( data.msg ) msg = data.msg;
				if( data.status == 1 ) cls = 'success';
				else if( data.status == 2 ) cls = 'message';
				else if( data.status == 3 ) cls = 'error';
				else{
					fld = 1;
					cls = 'error';
					msg = 'Failed to explode this data, please check your ajax file.';
				}
				
				console.log(data);
				
				$('<div id="'+cls+'"></div>')
				.text(msg)
				.prependTo('.modal-status')
				.delay(1000)
				.fadeOut(1000, function(){
					
					if( cls != 'error' && fld != 1 )
					document.location.reload();
					
					$(this).remove();
				});
			}
		})
	}

})();