<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
* Blog : blog.bilboplanet.com
* 
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
***** END LICENSE BLOCK *****/
?>
<?php
require_once(dirname(__FILE__).'/../inc/admin/prepend.php');
if ($core->auth->sessionExists()):
	if (!$core->hasRole('manager') | !$core->hasPermission('administration')){
		__error(T_("Permission denied"),
			T_('You are not allowed to see this page.')
			.' '.T_('You can delete your session if you logout : ').'<a href="?logout">Logout</a>');
		exit;
	}
debutCache();

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>
<div id="BP_page" class="page">
	<div class="inpage">

<div id="flash-log" style="display:none;">
	<div id="flash-msg"><!-- spanner --></div>
</div>

<div class="button br3px" id="add-form"><a onclick="jQuery('#addfeed-field').css('display', '')">
	<?php echo T_('Add a feed'); ?></a>
</div>
<div class="button br3px" id="filter-form"><a onclick="jQuery('#filterfeed-field').css('display', '')">
	<?php echo T_('Filter feedlist'); ?></a>
</div></p>
</p>


<fieldset id="filterfeed-field" style="display:none"><legend><?php echo T_('Filter');?></legend>
	<br/>

<?php

# Traitement de la liste
$rs = $core->con->select('SELECT DISTINCT
		'.$core->prefix.'user.user_id,
		user_fullname
	FROM '.$core->prefix.'user, '.$core->prefix.'feed
	WHERE '.$core->prefix.'feed.user_id = '.$core->prefix.'user.user_id
	ORDER BY user_fullname ASC;');
$users = array();
$users['-- '.T_('All').' --'] = 'all';
while($rs->fetch()) {
	$users["$rs->user_fullname"] = $rs->user_id;
}

$status = array();
$status['-- '.T_('All').' --'] = "all";
$status[T_('Active feeds')] = 1;
$status[T_('Inactive feeds')] = 0;
if ($blog_settings->get('auto_feed_disabling')){
	$status[T_('Auto disabled feeds')] = 2;
}
echo
'<form id="filterfeed_form">'.

'<label class="required" for="fuser_id">'.T_('User id').' : '.
form::combo('fuser_id',$users,'', 'input','','').'</label><br /><br />'.

'<label class="required" for="feed_status">'.T_('Feed status').' : '.
form::combo('feed_status',$status,'', 'input','','').'</label><br /><br />';

echo 
'<div class="button br3px"><input type="submit" name="filter_feed" class="valide" value="'.T_('Filter').'" /></div>'.
'<div class="button br3px close-button"><a class="close" onclick="jQuery(\'#filterfeed-field\').css(\'display\', \'none\');updateFeedList(0, 30)">'.T_('Close').'</a></div>'.
'</form>';
?>
</fieldset>


<fieldset id="addfeed-field" style="display:none"><legend><?php echo T_('Add a feed');?></legend>
	<div class="message">
		<p><?php echo T_("Manage member's feeds"); ?></p>
	</div><br/>

<?php


# Traitement de la liste
$rs = $core->con->select('SELECT DISTINCT
		'.$core->prefix.'user.user_id,
		user_fullname
	FROM '.$core->prefix.'user, '.$core->prefix.'site
	WHERE '.$core->prefix.'site.user_id = '.$core->prefix.'user.user_id
	ORDER BY user_fullname ASC;');
$users = array();
$users[T_('-- Choose an user --')] = '';
while($rs->fetch()) {
	$users["$rs->user_fullname"] = $rs->user_id;
}

echo
'<form id="addfeed_form">'.

'<label class="required" for="user_id">'.T_('User id').' : '.
form::combo('user_id',$users,'', 'input','','',"onchange=\"javascript:updateSiteCombo()\"").'</label><br /><br />'.

'<label class="required" for="site_id">'.T_('Site id').' : '.
form::combo('site_id',array(T_('-- Choose an user id --') => ''),'', 'input').'</label>
<span class="description">'.T_('Choose the website of the feed').'</span><br />'.

'<label class="required" for="feed_url">'.T_('Full feed URL').' : '.
form::field('feed_url',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: http://blog.bilboplanet.com/feed/atom/').'</span><br />'.

'<label class="required" for="feed_name">'.T_('Feed name').' : '.
form::field('feed_name',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: gnu/Linux posts').'</span><br />';

if ($blog_settings->get('planet_moderation')) {
	echo '<label class="required" for="feed_trust">'.T_('Trusted URL').' : '.
	form::combo('feed_trust',array('true' => '1', 'false' => '0'),'true', 'input').'</label><br /><br />';
}

echo 
'<div class="button br3px"><input type="reset" class="reset" name="reset" onClick="this.form.reset()" value="'.T_('Reset').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_feed" class="valide" value="'.T_('Add').'" /></div>'.
'<div class="button br3px close-button"><a class="close" onclick="jQuery(\'#addfeed-field\').css(\'display\', \'none\')">'.T_('Close').'</a></div>'.
'</form>';
?>
</fieldset>

<fieldset><legend><?php echo T_('Manage feeds');?></legend>
	<div class="message">
		<p><?php echo T_('Manage member feed.');?></p>
	</div>
	<div id="feed-list"></div>
</fieldset>

<div id="feed-edit-form" style="display:none">
<?php
echo '<form>'.
form::hidden('ef_id','').

'<label class="required" for="ef_user_id">'.T_('User id').' : '.
form::field('ef_user_id',30,255,html::escapeHTML(""), 'input').'</label><br />'.

'<label for="ef_name">'.T_('Feed name').' : '.
form::field('ef_name',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: GNU/Linux posts').'</span><br />'.

'<label class="required" for="ef_url">'.T_('Feed URL').' : '.
form::field('ef_url',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: http://blog.bilboplanet.com/feed/').'</span><br />'.

'<div class="button br3px"><input type="button" class="notvalide" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_user" class="valide" value="'.T_('Update').'" /></div>'.
'</form>';
?>
</div>


<script type="text/javascript" src="meta/js/manage-feed.js"></script>
<script type="text/javascript" src="meta/js/jquery.boxy.js"></script>
<?php 
include(dirname(__FILE__).'/footer.php');
finCache();
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('auth.php?came_from='.$page_url);
endif;
?>