/*
Post Edit Feature
Author: BeeTeam368
Author URI: http://themeforest.net/user/beeteam368
License: Themeforest Licence
License URI: http://themeforest.net/licenses
*/

;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports !== 'undefined') {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(function ($) {
    'use strict';

    var $d = $(document);
    var $w = $(window);
    var _d = document;
    var _w = window;
    var $h = $('html');
    var $b = $('body');
	
	$.getStylesheet = function (href) {
        var $d = $.Deferred();
        var $link = $('<link/>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: href
        }).appendTo('head');
        $d.resolve($link);
        return $d.promise();
    }

    $.getUrlExtension = function(url) {
        return url.split(/[#?]/)[0].split('.').pop().trim();
    }
	
	$.updateQueryStringParameterCT = function(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }else {
            return uri + separator + key + "=" + value;
        }
    }
	
	$.cachedScriptCT = function( url, options ) {
		options = $.extend( options || {}, {
			dataType: 'script',
			cache: true,
			url: $.updateQueryStringParameterCT(url, 'cache_version', vidmov_jav_js_object.cache_version)
		});
		return $.ajax( options );		
	}

    $.getMultiScripts = function(arr, path) {
        var _arr = $.map(arr, function(scr) {
            if($.getUrlExtension(scr) === 'css'){
                return $.getStylesheet( (path||'') + scr );
            }else if($.getUrlExtension(scr) === 'js'){
                return $.cachedScriptCT( (path||'') + scr );
            }

        });

        _arr.push($.Deferred(function( deferred ){
            $( deferred.resolve );
        }));

        return $.when.apply($, _arr);
    }
	
	$.isNumber = function(n){
		var _ = this;
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	$d.on('click', '.beeteam368_post-edit-control', function (e) {
		if ( typeof window.FormData !== 'function' ) {
			return;
		}
		
		var $t = $(this);		
		var $form = $t.parents('.form-submit-edit-control');
		var $alerts= $('.form-submit-edit-alerts-control');
		$alerts.html('');
		
		if(typeof(tinyMCE) !== 'undefined' && typeof(vidmov_jav_js_object.tinymce_description) !== 'undefined' && vidmov_jav_js_object.tinymce_description === 'on'){
			tinyMCE.triggerSave();
			$form.serialize(); 
		}
		
		var $parent = $t.parents('.beeteam368-global-popup-content-control');
		
		var formData = new FormData( $form.get(0) );		
		formData.append('action', 'beeteam368_handle_submit_edit_fn_fe');
		formData.append('security', vidmov_jav_js_object.security);
		
		$t.addClass('active-loading').blur();
		$parent.addClass('active-loading');
		
		$.ajax({
			type: 		'POST',
			url: 		vidmov_jav_js_object.admin_ajax,
			cache: 		false,
			data: 		formData,
			dataType: 	'json',
			processData: false,
			contentType: false,
			success: 	function(data, textStatus, jqXHR){
				if(typeof(data.info) !== 'undefined' && $.trim(data.info)!=''){
					$alerts.html(data.info);
				}
				
				if(typeof(data.status) !== 'undefined' && data.status === 'success'){
					_w.location.reload();
				}
				
				$t.removeClass('active-loading').blur();
				$parent.removeClass('active-loading');
				
				$d.trigger('beeteam368SubmitEditPostFormFinish', [$t, $form, formData]);
			},
			error: function( jqXHR, textStatus, errorThrown ){
				$t.removeClass('active-loading').blur();
				$parent.removeClass('active-loading');
			}
		});
		
	});
	
	$d.on('click', '.replace-source-control', function (e) {
		var r = confirm(vidmov_jav_js_object.dictConfirmDeleteSource);
		var $t = $(this);
		var $parent = $t.parents('.replace-source-wrapper-control');
		var post_id = $t.attr('data-id');
		var attach_id = $t.attr('data-att-id'); 
		var rem_action = $t.attr('data-action'); 
		
		if(r == true && $.isNumber(post_id)){
			$t.addClass('active-loading').blur();
			
			var query = {
				'action': 'beeteam368_author_change_source_post',
				'security': vidmov_jav_js_object.security,
				'post_id': post_id,
				'attach_id': attach_id,
				'rem_action': rem_action,
			}
			
			$.ajax({
				type: 		'POST',
				url: 		vidmov_jav_js_object.admin_ajax,
				cache: 		false,
				data: 		query,
				dataType: 	'json',				
				success: 	function(data, textStatus, jqXHR){
					if(typeof(data.status)!=='undefined' && data.status === 'error'){
						alert(data.info);
						$t.removeClass('active-loading').blur();
					}else{
						$parent.remove();
					}
				},
				error: function( jqXHR, textStatus, errorThrown ){
					$t.removeClass('active-loading').blur();										
				}
			});
		}
	});
	
	$d.on('click', '.button-delete-post-control', function (e) {
		var r = confirm(vidmov_jav_js_object.dictConfirmDeletePost);
		var $t = $(this);
		var post_id = $t.attr('data-id'); 
		
		if(r == true && $.isNumber(post_id)){
			
			$t.addClass('active-loading').blur();
			
			var query = {
				'action': 'beeteam368_author_delete_post',
				'security': vidmov_jav_js_object.security,
				'post_id': post_id,
			}
			
			$.ajax({
				type: 		'POST',
				url: 		vidmov_jav_js_object.admin_ajax,
				cache: 		false,
				data: 		query,
				dataType: 	'json',				
				success: 	function(data, textStatus, jqXHR){
					if(typeof(data.status)!=='undefined' && data.status === 'error'){
						alert(data.info);
						$t.removeClass('active-loading').blur();
					}else{
						alert(data.info);
						_w.location.href = data.file_link;
					}
				},
				error: function( jqXHR, textStatus, errorThrown ){
					$t.removeClass('active-loading').blur();										
				}
			});
			
		}
	});
	
	$b.on('beeteam368BeforeClickOpenPopupAction', function(e, t, popup_spe, popup, popup_content, action){
		if(action === 'open_submit_post_edit_popup'){
			
			var $alerts= $('.form-submit-edit-alerts-control');
			$alerts.html('');
			
			popup_content.addClass('active-loading');
			
			var rnd_id = 'submit-post-add-'+(Math.floor(Math.random() * 99999));			
			popup_content.find('.beeteam368-submit-post-add-wrapper-control').attr('id', rnd_id);
			
			var instance = OverlayScrollbars(document.getElementById(rnd_id), {
				scrollbars: {
					autoHide: 'leave',
				},
				overflowBehavior: {
					x: 'hidden',
					y: 'scroll',
				}				
			});		
			
			$d.on('beeteam368SubmitEditPostFormFinish', function(){
				instance.scroll({ y : 0 }, 368);
			});
			
			var params = {
				'id': rnd_id,
				'popup_content': popup_content,
				'post_id': t.attr('data-id'),
			};
			
			$('#'+(rnd_id)).beeteam368_create_edit_posts(params);
		}
	});
	
	$.fn.beeteam368_create_edit_posts = function($params){
		if(typeof($params) !== 'object'){
            return false;
        }
		
		var $t = $(this);
		
		var fN_params = [];		
		fN_params['id'] = $params['id'];
		fN_params['popup_content'] = $params['popup_content'];
		fN_params['post_id'] = $params['post_id'];
		
		var form_submit_id = fN_params['id'];
				
		var $wrapper = $('#'+(form_submit_id));
		var $form_html = $wrapper.find('.form-submit-edit-control');
		
		if(typeof(tinyMCE) !== 'undefined' && typeof(vidmov_jav_js_object.tinymce_description) !== 'undefined' && vidmov_jav_js_object.tinymce_description === 'on'){						
			tinymce.remove('#post_descriptions-edit');
		}
		
		$form_html.html('');
		
		$t.on('beeteam368DropZoneLibraryControlInstalled'+(form_submit_id), function(e, fN_params){
			
			var query = {
				'action': 'beeteam368_author_get_edit_form_html',
				'security': vidmov_jav_js_object.security,
				'post_id': fN_params['post_id'],
			}
			
			$.ajax({
				type: 		'POST',
				url: 		vidmov_jav_js_object.admin_ajax,
				cache: 		false,
				data: 		query,
				dataType: 	'html',				
				success: 	function(data, textStatus, jqXHR){
					
					$form_html.html(data);
					
					if(typeof(tinyMCE) !== 'undefined' && typeof(vidmov_jav_js_object.tinymce_description) !== 'undefined' && vidmov_jav_js_object.tinymce_description === 'on'){						
						tinymce.execCommand('mceAddEditor', true, 'post_descriptions-edit');
					}
					
					var popup_content = fN_params['popup_content'];
					
					popup_content.off('.switchSourceEditMode').on('click.switchSourceEditMode', '.btn-mode-source-control', function(e){
						var $t = $(this);				
						var source = $t.attr('data-source');				
						popup_content.find('.media-type-control').val(source);
						
						popup_content.find('.btn-mode-source-control').removeClass('primary-color-focus');
						$t.addClass('primary-color-focus');
						
						if(source === 'upload'){
							popup_content.find('.media-upload-hide-control').removeClass('is-temp-hidden');
							popup_content.find('.external-link-hide-control').addClass('is-temp-hidden');
						}else{
							popup_content.find('.media-upload-hide-control').addClass('is-temp-hidden');
							popup_content.find('.external-link-hide-control').removeClass('is-temp-hidden');
						}
					});
					
					popup_content.off('.switchPreviewSourceEditMode').on('click.switchPreviewSourceEditMode', '.btn-preview-mode-source-control', function(e){
						var $t = $(this);				
						var source = $t.attr('data-source');				
						popup_content.find('.preview-type-control').val(source);
						
						popup_content.find('.btn-preview-mode-source-control').removeClass('primary-color-focus');
						$t.addClass('primary-color-focus');
						
						if(source === 'upload'){
							popup_content.find('.preview-upload-hide-control').removeClass('is-temp-hidden');
							popup_content.find('.preview-external-link-hide-control').addClass('is-temp-hidden');
						}else{
							popup_content.find('.preview-upload-hide-control').addClass('is-temp-hidden');
							popup_content.find('.preview-external-link-hide-control').removeClass('is-temp-hidden');
						}
					});
					
					var $media_data_control = $wrapper.find('.media-data-control');					
					var $beeteam368_media_upload = $wrapper.find('.beeteam368_media-edit_upload-control');
					if($beeteam368_media_upload.length > 0 && !$beeteam368_media_upload.hasClass('dropzone_Installed')){
						const beeteam368_handle_media_upload = new Dropzone('div.beeteam368_media-edit_upload-control', { 
							chunking: true,
							forceChunking: true,
							chunkSize: 1024 * 1024 * parseInt(vidmov_jav_js_object.media_maxChunkSize), /*byte*/
							maxFilesize: vidmov_jav_js_object.media_maxFilesize, /*mb*/
							clickable: '.beeteam368_media-edit_upload-btn-control',
							disablePreviews: false,
							previewsContainer: '.media-edit_upload_preview_control',
							uploadMultiple: false,
							parallelChunkUploads: false,
							parallelUploads: 1,
							maxFiles: 1,
							addRemoveLinks: true,
							acceptedFiles: vidmov_jav_js_object.media_acceptedFiles,
							
							dictFileTooBig: vidmov_jav_js_object.dictFileTooBig,
							dictInvalidFileType: vidmov_jav_js_object.dictInvalidFileType,
							dictResponseError: vidmov_jav_js_object.dictResponseError,
							dictCancelUpload: vidmov_jav_js_object.dictCancelUpload,
							dictCancelUploadConfirmation: vidmov_jav_js_object.dictCancelUploadConfirmation,
							dictRemoveFile: vidmov_jav_js_object.dictRemoveFile,
							dictRemoveFileConfirmation: vidmov_jav_js_object.dictRemoveFileConfirmation,
							dictMaxFilesExceeded: vidmov_jav_js_object.dictMaxFilesExceeded,
												
							url: vidmov_jav_js_object.admin_ajax,
							
							chunksUploaded: function(file, done){
		
								let currentFile = file;
								
								var query = {
									'action': 'beeteam368_handle_submit_upload_chunk_concat_file_fe',
									'security': vidmov_jav_js_object.security,
									'dzuuid': currentFile.upload.uuid,
									'dztotalchunkcount': currentFile.upload.totalChunkCount,
									'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
								}
								
								$.ajax({
									type: 		'POST',
									url: 		vidmov_jav_js_object.admin_ajax,
									cache: 		false,
									data: 		query,
									dataType: 	'json',				
									success: 	function(data, textStatus, jqXHR){
										if(typeof(data.status)!=='undefined' && data.status === 'error'){
											beeteam368_handle_media_upload._errorProcessing([currentFile], data.info);
										}else{
											done();
											if(typeof(data.status)!=='undefined' && data.status === 'success' && typeof(data.file_link)!=='undefined' && data.file_link != ''){
												$media_data_control.val(data.file_link);
											}
										}
									},
									error: function( jqXHR, textStatus, errorThrown ){
										currentFile.accepted = false;
										beeteam368_handle_media_upload._errorProcessing([currentFile], jqXHR.responseText);								
									}
								});
							},
							
							init: function(){
								
								let tdz = this;
								
								$beeteam368_media_upload.addClass('dropzone_Installed');
								
								tdz.on('dragover', function(file, xhr, formData){
									$beeteam368_media_upload.addClass('is-dragover');
								});
								
								tdz.on('dragleave', function(file, xhr, formData){	
									$beeteam368_media_upload.removeClass('is-dragover');
								});
								
								tdz.on('drop', function(file, xhr, formData){
									$beeteam368_media_upload.removeClass('is-dragover');
								});
								
								tdz.on('sending', function(file, xhr, formData){
									$beeteam368_media_upload.removeClass('is-dragover');
									$media_data_control.val('beeteam368_processing');
									
									formData.append('action', 'beeteam368_handle_submit_upload_file_fe');
									formData.append('security', vidmov_jav_js_object.security);	
								});
								
								tdz.on('success', function(file, xhr, formData){
								});
								
								tdz.on('error', function(file, xhr, formData){
								});
								
								tdz.on('canceled', function(file, xhr, formData){
									
									let file_path = $media_data_control.val();
									
									if(file_path!='' && typeof(file.upload) !== 'undefined' && typeof(file.upload.uuid) !== 'undefined'){
										var file_path_with_not_ext = file_path.substr( 0, (file_path.lastIndexOf('.')) );								
										if(file_path_with_not_ext === file.upload.uuid || file_path === 'beeteam368_processing'){
											$media_data_control.val('');
										}
									}
		
									let currentFile = file;							
									var query = {
										'action': 'beeteam368_handle_remove_temp_file',
										'security': vidmov_jav_js_object.security,
										'dzuuid': currentFile.upload.uuid,
										'dztotalchunkcount': currentFile.upload.totalChunkCount,
										'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
									}							
									$.ajax({
										type: 		'POST',
										url: 		vidmov_jav_js_object.admin_ajax,
										cache: 		false,
										data: 		query,
										dataType: 	'json',				
										success: 	function(data, textStatus, jqXHR){
											console.log('Removed Files');
										},
										error: function( jqXHR, textStatus, errorThrown ){	
											console.log('Error');
										}
									});
									
								});
								
								tdz.on('removedfile', function(file, xhr, formData){
									
									let file_path = $media_data_control.val();
									
									if(file_path!='' && typeof(file.upload) !== 'undefined' && typeof(file.upload.uuid) !== 'undefined'){
										var file_path_with_not_ext = file_path.substr( 0, (file_path.lastIndexOf('.')) );								
										if(file_path_with_not_ext === file.upload.uuid || file_path === 'beeteam368_processing'){
											$media_data_control.val('');
										}
									}
									
									let currentFile = file;							
									var query = {
										'action': 'beeteam368_handle_remove_temp_file',
										'security': vidmov_jav_js_object.security,
										'dzuuid': currentFile.upload.uuid,
										'dztotalchunkcount': currentFile.upload.totalChunkCount,
										'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
									}							
									$.ajax({
										type: 		'POST',
										url: 		vidmov_jav_js_object.admin_ajax,
										cache: 		false,
										data: 		query,
										dataType: 	'json',				
										success: 	function(data, textStatus, jqXHR){
											console.log('Removed Files');
										},
										error: function( jqXHR, textStatus, errorThrown ){	
											console.log('Error');
										}
									});
									
								});
								
								tdz.on('reset', function(){
								});
								
								$d.on('beeteam368SubmitPostFormSuccess', function(){
									tdz.removeAllFiles(true);
								});
							},
						});
					}
					
					var $preview_data_control = $wrapper.find('.preview-data-control');
					var $beeteam368_preview_upload = $wrapper.find('.beeteam368_preview-edit_upload-control');
					if($beeteam368_preview_upload.length > 0 && !$beeteam368_preview_upload.hasClass('dropzone_Installed')){
						const beeteam368_handle_preview_upload = new Dropzone('div.beeteam368_preview-edit_upload-control', { 
							chunking: true,
							forceChunking: true,
							chunkSize: 1024 * 1024 * parseInt(vidmov_jav_js_object.media_maxChunkSize), /*byte*/
							maxFilesize: vidmov_jav_js_object.media_maxFilesize, /*mb*/
							clickable: '.beeteam368_preview-edit_upload-btn-control',
							disablePreviews: false,
							previewsContainer: '.preview-edit_upload_preview_control',
							uploadMultiple: false,
							parallelChunkUploads: false,
							parallelUploads: 1,
							maxFiles: 1,
							addRemoveLinks: true,
							acceptedFiles: vidmov_jav_js_object.media_acceptedFiles,
							
							dictFileTooBig: vidmov_jav_js_object.dictFileTooBig,
							dictInvalidFileType: vidmov_jav_js_object.dictInvalidFileType,
							dictResponseError: vidmov_jav_js_object.dictResponseError,
							dictCancelUpload: vidmov_jav_js_object.dictCancelUpload,
							dictCancelUploadConfirmation: vidmov_jav_js_object.dictCancelUploadConfirmation,
							dictRemoveFile: vidmov_jav_js_object.dictRemoveFile,
							dictRemoveFileConfirmation: vidmov_jav_js_object.dictRemoveFileConfirmation,
							dictMaxFilesExceeded: vidmov_jav_js_object.dictMaxFilesExceeded,
		
							url: vidmov_jav_js_object.admin_ajax,
							
							chunksUploaded: function(file, done){
		
								let currentFile = file;
								
								var query = {
									'action': 'beeteam368_handle_submit_upload_chunk_concat_file_fe',
									'security': vidmov_jav_js_object.security,
									'dzuuid': currentFile.upload.uuid,
									'dztotalchunkcount': currentFile.upload.totalChunkCount,
									'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
								}
								
								$.ajax({
									type: 		'POST',
									url: 		vidmov_jav_js_object.admin_ajax,
									cache: 		false,
									data: 		query,
									dataType: 	'json',				
									success: 	function(data, textStatus, jqXHR){
										if(typeof(data.status)!=='undefined' && data.status === 'error'){
											beeteam368_handle_preview_upload._errorProcessing([currentFile], data.info);
										}else{
											done();
											if(typeof(data.status)!=='undefined' && data.status === 'success' && typeof(data.file_link)!=='undefined' && data.file_link != ''){
												$preview_data_control.val(data.file_link);
											}
										}
									},
									error: function( jqXHR, textStatus, errorThrown ){
										currentFile.accepted = false;
										beeteam368_handle_preview_upload._errorProcessing([currentFile], jqXHR.responseText);								
									}
								});
							},
							
							init: function(){
								
								let tdz = this;
								
								$beeteam368_preview_upload.addClass('dropzone_Installed');
								
								tdz.on('dragover', function(file, xhr, formData){
									$beeteam368_preview_upload.addClass('is-dragover');
								});
								
								tdz.on('dragleave', function(file, xhr, formData){	
									$beeteam368_preview_upload.removeClass('is-dragover');
								});
								
								tdz.on('drop', function(file, xhr, formData){
									$beeteam368_preview_upload.removeClass('is-dragover');
								});
								
								tdz.on('sending', function(file, xhr, formData){
									$beeteam368_preview_upload.removeClass('is-dragover');
									$preview_data_control.val('beeteam368_processing');
									
									formData.append('action', 'beeteam368_handle_submit_upload_file_fe');
									formData.append('security', vidmov_jav_js_object.security);	
								});
								
								tdz.on('success', function(file, xhr, formData){
								});
								
								tdz.on('error', function(file, xhr, formData){
								});
								
								tdz.on('canceled', function(file, xhr, formData){
									
									let file_path = $preview_data_control.val();
									
									if(file_path!='' && typeof(file.upload) !== 'undefined' && typeof(file.upload.uuid) !== 'undefined'){
										var file_path_with_not_ext = file_path.substr( 0, (file_path.lastIndexOf('.')) );								
										if(file_path_with_not_ext === file.upload.uuid || file_path === 'beeteam368_processing'){
											$preview_data_control.val('');
										}
									}
		
									let currentFile = file;							
									var query = {
										'action': 'beeteam368_handle_remove_temp_file',
										'security': vidmov_jav_js_object.security,
										'dzuuid': currentFile.upload.uuid,
										'dztotalchunkcount': currentFile.upload.totalChunkCount,
										'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
									}							
									$.ajax({
										type: 		'POST',
										url: 		vidmov_jav_js_object.admin_ajax,
										cache: 		false,
										data: 		query,
										dataType: 	'json',				
										success: 	function(data, textStatus, jqXHR){
											console.log('Removed Files');
										},
										error: function( jqXHR, textStatus, errorThrown ){	
											console.log('Error');
										}
									});
									
								});
								
								tdz.on('removedfile', function(file, xhr, formData){
									
									let file_path = $preview_data_control.val();
									
									if(file_path!='' && typeof(file.upload) !== 'undefined' && typeof(file.upload.uuid) !== 'undefined'){
										var file_path_with_not_ext = file_path.substr( 0, (file_path.lastIndexOf('.')) );								
										if(file_path_with_not_ext === file.upload.uuid || file_path === 'beeteam368_processing'){
											$preview_data_control.val('');
										}
									}
									
									let currentFile = file;							
									var query = {
										'action': 'beeteam368_handle_remove_temp_file',
										'security': vidmov_jav_js_object.security,
										'dzuuid': currentFile.upload.uuid,
										'dztotalchunkcount': currentFile.upload.totalChunkCount,
										'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
									}							
									$.ajax({
										type: 		'POST',
										url: 		vidmov_jav_js_object.admin_ajax,
										cache: 		false,
										data: 		query,
										dataType: 	'json',				
										success: 	function(data, textStatus, jqXHR){
											console.log('Removed Files');
										},
										error: function( jqXHR, textStatus, errorThrown ){	
											console.log('Error');
										}
									});
									
								});
								
								tdz.on('reset', function(){
								});
								
								$d.on('beeteam368SubmitPostFormSuccess', function(){
									tdz.removeAllFiles(true);
								});
							},
						});
					}
					
					var $featured_image_control = $wrapper.find('.featured-image-control');					
					var $beeteam368_featured_image_upload = $wrapper.find('.beeteam368_featured_image-edit_upload-control');
					if($beeteam368_featured_image_upload.length > 0 && !$beeteam368_featured_image_upload.hasClass('dropzone_Installed')){
						const beeteam368_handle_featured_image_upload = new Dropzone('div.beeteam368_featured_image-edit_upload-control', { 
							chunking: true,
							forceChunking: true,
							chunkSize: 1024 * 1024 * parseInt(vidmov_jav_js_object.featured_image_maxFilesize), /*byte*/
							maxFilesize: vidmov_jav_js_object.featured_image_maxFilesize, /*mb*/
							clickable: '.beeteam368_featured_image-edit_upload-btn-control',
							disablePreviews: false,
							previewsContainer: '.featured_image-edit_upload_preview_control',
							uploadMultiple: false,
							parallelChunkUploads: false,
							parallelUploads: 1,
							maxFiles: 1,
							addRemoveLinks: true,					
							acceptedFiles: '.jpg,.png,.gif,.bmp,.tif,.webp,.jpeg',
							
							dictFileTooBig: vidmov_jav_js_object.dictFileTooBig,
							dictInvalidFileType: vidmov_jav_js_object.dictInvalidFileType,
							dictResponseError: vidmov_jav_js_object.dictResponseError,
							dictCancelUpload: vidmov_jav_js_object.dictCancelUpload,
							dictCancelUploadConfirmation: vidmov_jav_js_object.dictCancelUploadConfirmation,
							dictRemoveFile: vidmov_jav_js_object.dictRemoveFile,
							dictRemoveFileConfirmation: vidmov_jav_js_object.dictRemoveFileConfirmation,
							dictMaxFilesExceeded: vidmov_jav_js_object.dictMaxFilesExceeded,
							
							url: vidmov_jav_js_object.admin_ajax,
							
							chunksUploaded: function(file, done){
		
								let currentFile = file;
								
								var query = {
									'action': 'beeteam368_handle_submit_upload_chunk_concat_file_fe',
									'security': vidmov_jav_js_object.security,
									'dzuuid': currentFile.upload.uuid,
									'dztotalchunkcount': currentFile.upload.totalChunkCount,
									'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
								}
								
								$.ajax({
									type: 		'POST',
									url: 		vidmov_jav_js_object.admin_ajax,
									cache: 		false,
									data: 		query,
									dataType: 	'json',				
									success: 	function(data, textStatus, jqXHR){
										if(typeof(data.status)!=='undefined' && data.status === 'error'){
											beeteam368_handle_featured_image_upload._errorProcessing([currentFile], data.info);
										}else{
											done();
											if(typeof(data.status)!=='undefined' && data.status === 'success' && typeof(data.file_link)!=='undefined' && data.file_link != ''){
												$featured_image_control.val(data.file_link);
											}
										}
									},
									error: function( jqXHR, textStatus, errorThrown ){
										currentFile.accepted = false;
										beeteam368_handle_featured_image_upload._errorProcessing([currentFile], jqXHR.responseText);								
									}
								});
							},
							
							init: function(){
								
								let tdz = this;
								
								$beeteam368_featured_image_upload.addClass('dropzone_Installed');
								
								tdz.on('dragover', function(file, xhr, formData){
									$beeteam368_featured_image_upload.addClass('is-dragover');
								});
								
								tdz.on('dragleave', function(file, xhr, formData){	
									$beeteam368_featured_image_upload.removeClass('is-dragover');
								});
								
								tdz.on('drop', function(file, xhr, formData){
									$beeteam368_featured_image_upload.removeClass('is-dragover');
								});
								
								tdz.on('sending', function(file, xhr, formData){
									$beeteam368_featured_image_upload.removeClass('is-dragover');
									$featured_image_control.val('beeteam368_processing');
									
									formData.append('action', 'beeteam368_handle_submit_upload_file_fe');
									formData.append('security', vidmov_jav_js_object.security);	
								});
								
								tdz.on('success', function(file, xhr, formData){
								});
								
								tdz.on('error', function(file, xhr, formData){
								});
								
								tdz.on('canceled', function(file, xhr, formData){
		
									let file_path = $featured_image_control.val();
									
									if(file_path!='' && typeof(file.upload) !== 'undefined' && typeof(file.upload.uuid) !== 'undefined'){
										var file_path_with_not_ext = file_path.substr( 0, (file_path.lastIndexOf('.')) );								
										if(file_path_with_not_ext === file.upload.uuid || file_path === 'beeteam368_processing'){
											$featured_image_control.val('');
										}
									}
		
									let currentFile = file;							
									var query = {
										'action': 'beeteam368_handle_remove_temp_file',
										'security': vidmov_jav_js_object.security,
										'dzuuid': currentFile.upload.uuid,
										'dztotalchunkcount': currentFile.upload.totalChunkCount,
										'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
									}							
									$.ajax({
										type: 		'POST',
										url: 		vidmov_jav_js_object.admin_ajax,
										cache: 		false,
										data: 		query,
										dataType: 	'json',				
										success: 	function(data, textStatus, jqXHR){
											console.log('Removed Files');
										},
										error: function( jqXHR, textStatus, errorThrown ){	
											console.log('Error');
										}
									});
									
								});
								
								tdz.on('removedfile', function(file, xhr, formData){
									
									let file_path = $featured_image_control.val();
									
									if(file_path!='' && typeof(file.upload) !== 'undefined' && typeof(file.upload.uuid) !== 'undefined'){
										var file_path_with_not_ext = file_path.substr( 0, (file_path.lastIndexOf('.')) );								
										if(file_path_with_not_ext === file.upload.uuid || file_path === 'beeteam368_processing'){
											$featured_image_control.val('');
										}
									}
									
									let currentFile = file;							
									var query = {
										'action': 'beeteam368_handle_remove_temp_file',
										'security': vidmov_jav_js_object.security,
										'dzuuid': currentFile.upload.uuid,
										'dztotalchunkcount': currentFile.upload.totalChunkCount,
										'fileName': currentFile.name.substr( (currentFile.name.lastIndexOf('.') +1) ),
									}							
									$.ajax({
										type: 		'POST',
										url: 		vidmov_jav_js_object.admin_ajax,
										cache: 		false,
										data: 		query,
										dataType: 	'json',				
										success: 	function(data, textStatus, jqXHR){
											console.log('Removed Files');
										},
										error: function( jqXHR, textStatus, errorThrown ){	
											console.log('Error');
										}
									});
									
								});
								
								tdz.on('reset', function(){
								});
								
								$d.on('beeteam368SubmitPostFormSuccess', function(){
									tdz.removeAllFiles(true);
								});
							},
						});
					}
                    
                    $d.trigger('beeteam368SubFeaturesEditPostForm', [fN_params]);
					
					var dir = 'ltr';
			
					if($b.css('direction')=='rtl'){
						dir = 'rtl';
					}
					
					if(typeof($.fn.select2)!=='undefined'){
						$('.select-multiple-edit-control').each(function(index, element) {
							$(this).select2({
								dir:dir,
							});
						});
					}
					
					fN_params['popup_content'].removeClass('active-loading');
				},
				error: function( jqXHR, textStatus, errorThrown ){															
				}
			});			
		});
		
		if(typeof(Dropzone) === 'function'){

			$t.trigger('beeteam368DropZoneLibraryControlInstalled'+(form_submit_id), [fN_params]);
			console.log('[Submit Library] Already Exist.');

		}else{
			
			var script_arr = [
				(vidmov_jav_js_object.upload_library_function_url)+'dropzone.css',
				(vidmov_jav_js_object.upload_library_function_url)+'dropzone-min.js',				
				(vidmov_jav_js_object.upload_library_function_url)+'select2.min.css',
				(vidmov_jav_js_object.upload_library_function_url)+'select2.full.min.js',
			];

			$.getMultiScripts(script_arr).done(function() {
				$t.trigger('beeteam368DropZoneLibraryControlInstalled'+(form_submit_id), [fN_params]);
				console.log('[Submit Library] Loaded.');
			}).fail(function(error) {
				console.log('[Submit Library] One or more scripts failed to load.');
			});
			
		}
	}

}));