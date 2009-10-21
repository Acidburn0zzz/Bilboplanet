<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.org
* Website : www.bilboplanet.org
* Tracker : redmine.bilboplanet.org
* Blog : blog.bilboplanet.org
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
require_once(dirname(__FILE__).'/inc/i18n.php');
require_once(dirname(__FILE__).'/inc/fonctions.php');
include(dirname(__FILE__).'/head.php');
?>

<div id="centre">
<?php
include_once(dirname(__FILE__).'/sidebar.php');
?>
<div id="centre_centre">
<center>
<h3><?=T_('Error 404');?></h3>
<img src="themes/<?php echo $planet_theme; ?>/images/404.png">
<p><?=T_("Page not found");?></p>
</center>

<?php
include_once(dirname(__FILE__).'/sidebar.php');
include(dirname(__FILE__).'/footer.php');
?>