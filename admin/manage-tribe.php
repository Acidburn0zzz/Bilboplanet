<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
* Blog : www.bilboplanet.com
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

<div class="button br3px" id="add-form"><a onclick="javascript:openAdd()">
	<?php echo T_('Create a tribe'); ?></a>
</div>
</p>
</p>

<fieldset id="addtribe-field" style="display:none"><legend><?php echo T_('Create a tribe');?></legend>
	<div class="message">
		<p><?php echo T_("Manage tribes. You can add user filters, search filters and tag filters. You can add tribes for all users or for specific users."); ?></p>
	</div><br/>

<?php


# Traitement de la liste
$rs = $core->con->select('SELECT DISTINCT
		'.$core->prefix.'user.user_id,
		user_fullname,
		lower(user_fullname) AS user_fullname_lower
	FROM '.$core->prefix.'user, '.$core->prefix.'site
	WHERE '.$core->prefix.'site.user_id = '.$core->prefix.'user.user_id
	ORDER BY lower(user_fullname) ASC;');
$users = array();
$users[T_('-- Choose an user --')] = '';
$users[T_('-- ALL USERS --')] = 'root';
while($rs->fetch()) {
	$users["$rs->user_fullname"] = urlencode($rs->user_id);
}

echo
'<form id="addtribe_form">'.

'<label class="required" for="user_id">'.T_('Owner').' : '.
form::combo('user_id',$users,'', 'input','','',"").'</label><br /><br />'.

'<label class="required" for="tribe_name">'.T_('Tribe name').' : '.
form::field('tribe_name',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('ex: climate change').'</span><br />'.

'<label class="required" for="ordering">'.T_('Ordering').' : '.
form::field('ordering',30,255,html::escapeHTML(""), 'input').'</label>
<span class="description">'.T_('The ordering specifies the position of your tribe on the portal.').'</span><br />';

echo
'<div class="button br3px"><input type="reset" class="reset" name="reset" value="'.T_('Reset').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_feed" class="valide" value="'.T_('Add').'" /></div>'.
'<div class="button br3px close-button"><a class="close" onclick="javascript:closeAdd()">'.T_('Close').'</a></div>'.
'</form>';
?>
</fieldset>

<fieldset><legend><?php echo T_('Manage tribes');?></legend>
	<div class="message">
		<p><?php echo T_('Manage tribes.');?></p>
	</div>
	<div id="tribe-list"></div>
</fieldset>

<div id="tribe-edit-form" style="display:none">
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
<span class="description">'.T_('ex: http://www.bilboplanet.com/feed/').'</span><br />'.

'<div class="button br3px"><input type="button" class="notvalide" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>&nbsp;&nbsp;'.
'<div class="button br3px"><input type="submit" name="add_user" class="valide" value="'.T_('Update').'" /></div>'.
'</form>';
?>
</div>

<div id="tag-tribe-form" style="display:none">
<?php
echo '<form>'.

'<label class="required" for="content">'.T_('Add new tags').' : <br />'.
form::field('tags',30,255,html::escapeHTML(""), 'input').'</label><br/>
<span class="description"><i>'.T_('Comma separated tags (ex: linux,web,event)').'</i></span><br /><br />'.

'<div class="button"><input type="button" class="cancel" name="cancel" onClick="Boxy.get($(\'form.boxform\')).hide()" value="'.T_('Cancel').'"></div>'.
'<div class="button"><input type="submit" name="send" class="add_site" value="'.T_('Send').'" /></div>'.
'</form>';
?>
</div>


<script type="text/javascript" src="meta/js/manage-tribe.js"></script>
<script type="text/javascript" src="meta/js/jquery.boxy.js"></script>
<?php
include(dirname(__FILE__).'/footer.php');
finCache();
else:
	$page_url = urlencode(http::getHost().$_SERVER['REQUEST_URI']);
	http::redirect('../auth.php?came_from='.$page_url);
endif;
?>
