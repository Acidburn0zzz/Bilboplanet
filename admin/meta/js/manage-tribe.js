this.isfilter = false;
$(document).ready(function() {
	this.isfilter=false;
	$('#addtribe_form').submit(function() {
		var data = $('#addtribe_form').serialize();
		$('#flash-log').css('display','');
		$('#flash-msg').addClass('ajax-loading');
		$('#flash-msg').html('Loading');
		$.ajax({
			type: "POST",
			url: "api/",
			data: 'ajax=tribe&action=add&'+data,
			success: function(msg){
				$('#addtribe-field').css('display', 'none');
				$('#addtribe_form').find('input[type=text]').val('');
				$('#user_id').val('');
				$('#tribe_name').val('');
				$('#ordering').val('');
				updateTribeList();
				$('#flash-msg').html('');
				$('#flash-msg').removeClass('ajax-loading');
				$('#flash-log').css('display', 'none');
				$(msg).flashmsg();
			}
		});
		return false;
	});
	$("#tribe-list").ready( function() {
		updateTribeList();
	});
});

function updateTribeList(num_page, nb_items) {
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribe', 'action' : 'list', 'num_page': num_page, 'nb_items' : nb_items},
		success: function(msg){
			$("#tribe-list").html(msg);
		}
	});
}

function toggleTribeVisibility(tribe_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribe', 'action' : 'toggle', 'tribe_id' : tribe_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
			$(msg).flashmsg();
		}
	});
}


function removeTribe(tribe_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data : {'ajax' : 'tribe', 'action' : 'remove', 'tribe_id' : tribe_id},
		success: function(msg){
			$('#flash-msg').removeClass('ajax-loading');
			$('#flash-msg').html('');
			$(msg).flashmsg();
			$('#removeTribeConfirm_form').submit(function() {
				var data = $('#removeTribeConfirm_form').serialize().split('=');
				$('#flash-msg').addClass('ajax-loading');
				$('#flash-msg').html('Loading');
				$.ajax({
					type: "POST",
					url: "api/",
					data : {'ajax' : 'tribe', 'action' : 'removeConfirm', 'tribe_id' : data[1]},
					success: function(msg){
						updateTribeList(num_page, nb_items);
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-msg').html('');
						$('#flash-log').css('display', 'none');
						updateTribeList(num_page, nb_items);
						$(msg).flashmsg();
					}
				});
				return false;
			});
		}
	});
}

function edit(tribe_id, num_page, nb_items) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
	$.ajax({
		type: "POST",
		url: "api/",
		data: {'ajax' : 'tribe', 'action' : 'get', 'tribe_id' : tribe_id},
		dataType: 'json',
		success: function(feed){
			$('#feed-edit-form #ef_id').val(feed.feed_id);
			$('#feed-edit-form #ef_user_id').val(feed.user_id);
			$('#feed-edit-form #ef_user_id').attr('disabled', 'true');
			$('#feed-edit-form #ef_name').val(feed.feed_name);
			$('#feed-edit-form #ef_url').val(feed.feed_url);
			var content = $('#feed-edit-form form').clone();

			Boxy.askform(content, function(val) {
				val['ajax'] = "tribe";
				val['action'] = "edit";
				$.ajax({
					type: "POST",
					url: "api/",
					data : val,
					success: function(msg){
						$('#feed-edit-form #ef_user_id').removeAttr('disabled');
						$('#feed-edit-form #ef_user_id').val('');
						$('#feed-edit-form #ef_id').val('');
						$('#feed-edit-form #ef_name').val('');
						$('#feed-edit-form #ef_url').val('');
						$('#flash-msg').removeClass('ajax-loading');
						$('#flash-log').css('display', 'none');
						updateTribeList(num_page, nb_items);
						$(msg).flashmsg();
					}
				});
			}, {
				title: "Update "+tribe.user_id+" tribes",
				closeable: true,
			});
		}
	});
	return false;
}

function openAdd() {
	jQuery('#addtribe-field').css('display', '');
}

function closeAdd() {
	jQuery('#addtribe-field').css('display', 'none');
}

function rm_tag(num_page, nb_items, tribe_id, tag) {
	$('#flash-log').css('display','');
	$('#flash-msg').addClass('ajax-loading');
	$('#flash-msg').html('Loading');
    $.ajax({
        type: "POST",
        url: "api/",
        data : {'ajax' : 'tribe', 'action' : 'rm_tag', 'tribe_id' : tribe_id, 'tag' : tag},
        success: function(msg){
//            $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
            $('#flash-msg').removeClass('ajax-loading');
            $('#flash-msg').html('');
            $('#flash-log').css('display', 'none');
			updateTribeList(num_page, nb_items);
            $(msg).flashmsg();
        }
    });
}

function add_tags(num_page, nb_items, tribe_id, tribe_name) {
    var content = $('#tag-tribe-form form').clone();

    Boxy.askform(content, function(val) {
        $('#flash-log').css('display','');
        $('#flash-msg').addClass('ajax-loading');
        $("#flash-msg").html('Sending');
//        $("#tag_action"+tribe_id)[0].setAttribute('class','ajax-loading');
		var data = content.serialize().split('=');
        $.ajax({
            type: "POST",
            url: "api/",
			data : {'ajax' : 'tribe', 'action' : 'add_tags', 'tribe_id' : tribe_id, 'tags' : data[1]},
            success: function(msg){
//                $("#tag_action"+tribe_id)[0].removeAttribute('class','ajax-loading');
                $('#flash-msg').removeClass('ajax-loading');
                $('#flash-log').css('display', 'none');
                $(msg).flashmsg();
				updateTribeList(num_page, nb_items);
            }
        });
    }, {
        title: "Tagging : " + tribe_name,
    });
}