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
# Inclusion des fonctions
require_once(dirname(__FILE__).'/../inc/fonctions.php');
debutCache();
# Parametre par defaut
$num_membre  = 0;
$flash='';
# Valeurs par defaut
$num_page = 0;
$num_start = 0;
$nb_items = 30;


# Verification du contenu du get
if (isset($_POST) && isset($_POST['nb_items']) && !empty($_POST['nb_items'])){
	$nb_items = $_POST['nb_items'];
}
if (isset($_GET) && isset($_GET['nb_items']) && !empty($_GET['nb_items'])){
	$nb_items = $_GET['nb_items'];
}
if (isset($_GET) && isset($_GET['page']) && is_numeric(trim($_GET['page']))) {
	# On recuepre la valeur du get
	$num_page = trim($_GET['page']);
	if ($num_page < 1) {
		$num_page = 0;
	}
	$num_start = $num_page * $nb_items;
}

# Si il y a filtrage sur le membre
if(isset($_GET['num_membre']) && !empty($_GET['num_membre'])) {
	$num_membre = trim($_GET['num_membre']);
}
elseif(isset($_POST['num_membre']) && is_numeric(trim($_POST['num_membre']))) {
	$num_membre = trim($_POST['num_membre']);
}

if(isset($_POST) && (
    (isset($_POST['submitModif']) && !empty($_POST['submitModif'])) ||
    (isset($_POST['submitDelete']) && !empty($_POST['submitDelete']))
))
# On verifie que le formulaire est bien saisie
if(isset($_POST) && isset($_POST['num']) && isset($_POST['statut'])) {

	securiteCheck();
	# On recupere les infos
	$num = trim($_POST['num']);
	$statut = trim($_POST['statut']);
	$action = trim($_POST['submitModif']);
	$action = trim($_POST['submitDelete']);

	# Connection a la base
	connectBD();

	# On insert une nouvelle entree
	if(isset($_POST) && isset($_POST['submitDelete']) && !empty($_POST['submitDelete']))
		$sql = "DELETE FROM article WHERE num_article='$num'";
	else
		$sql = "UPDATE article
		SET article_statut = '$statut'
		WHERE num_article = '$num'";
	$result = mysql_query($sql) or die("Error with request $sql");

	# Femeture de la base
	closeBD();

	if($result) {
		$flash = array('type' => 'notice', 'msg' => sprintf(T_("The post %s was changed"),$num['value']));
	} else {
		$flash = array('type' => 'error', 'msg' => sprintf(T_("Error while trying to modify post %s !"),$num['value']));
	}
}

# Connection a la base 
connectBD();

include_once(dirname(__FILE__).'/head.php');
include_once(dirname(__FILE__).'/sidebar.php');
?>

<div id="BP_page" class="page">
	<div class="inpage">

<?php if (!empty($flash)) echo '<div class="flash '.$flash['type'].'">'.$flash['msg'].'</div>';?>

<fieldset><legend><?=T_('Filtering of the posts');?></legend>
		<div class="message">
			<p>Filtrer les actualit&eacute;s</p>
		</div><br />

<form action="" method="POST">
<table class="table-news">
<tr>
<td style="width:150px;border-right-width: 1px;border-right-style: solid;background-color:#D8D8D8;"><?=T_('Posts of the member :');?></td>
<td style="background-color:#D8D8D8;"><center><select name="num_membre" style="width:180px;">

<?php
# Execution de la requete
$sql = 'SELECT num_membre, nom_membre FROM membre ORDER BY nom_membre ASC;';
$rqt = mysql_query($sql) or die("Error with request $sql");

# Traitement de la liste
while($liste = mysql_fetch_row($rqt)) {
	if($num_membre == $liste[0]) {
		echo '<option value="'.$liste[0].'" selected>'.$liste[1].'</option>';
	} else {
		echo '<option value="'.$liste[0].'">'.$liste[1].'</option>';
	}
}

# On rajoute l'option Tous
if($num_membre == "0") {
	echo '<option value="'.$liste[0].'" selected>'.T_('All').'</option>';
} else {
	echo '<option value="'.$liste[0].'">Tous</option>';
}
?>
</select></center></td>
</tr>
<tr>
<td style="border-right-width: 1px;border-right-style: solid;"><?=T_('Number of posts');?></td>
<td><center><input type="text" class="input" style="text-align:center;width:170px;" name="nb_items"  value="<?php echo $nb_items; ?>" /></center></td>
</tr>
</table>
<p style="padding-top:70px">
<div class="button"><input type="reset" value="<?=T_('Reset');?>" class="reset" onClick="this.form.reset()"></div>&nbsp;&nbsp;
<div class="button"><input type="submit" class="valide" value="<?=T_('Send');?>"></div></p>

<br/>
</form>
</fieldset>

<fieldset><legend><?=T_('List of the posts')?></legend>
		<div class="message">
			<p><?=T_('NOTE: If you delete a post which is too recent, it will be refetched next time the update happen !!');?></p>
		</div><br />

<?php

# Execution de la requete
$sql = "SELECT * FROM article ORDER BY num_article ASC LIMIT $num_start,$nb_items;";
$rqt = mysql_query($sql) or die("Error with request $sql");

include(dirname(__FILE__).'/pagination.php');
?>
<table class="table-results sortable">
		<thead>
			<tr>
				<th style="width:10%;" scope="col"><?=T_('Name');?></th>
				<th style="width:120px;" scope="col"><?=T_('Date');?></th>
				<th class="tc3" scope="col"><?=T_('Title');?></th>
				<th class="tc4" scope="col" ><?=T_('Status');?></th>
				<th style="width:20px;" scope="col"><?=T_('Nb votes');?></th>
				<th  style="width:160px;" scope="col"><?=T_('Action');?></th>
			</tr>
		</thead>

<?php

# Debut de la requete
$sql = "SELECT num_article, nom_membre, article_pub, article_titre, article_statut, article_score,article_url
	FROM article, membre 
	WHERE article.num_membre = membre.num_membre ";

# Si on filtre un membre
if($num_membre != 0) $sql .= "AND article.num_membre = '$num_membre'"; 

# Fin de la requete
$sql .= "ORDER by article_pub DESC LIMIT $num_start,$nb_items";

# Execution de la requete
$rqt = mysql_query($sql) or die("Error with request $sql");

/* Traitement de la liste */
while($liste = mysql_fetch_row($rqt)) {

	# Construction de l'url
	$url = $liste[3].$liste[1];

	# Formatage de la date
	$date = date("d/m/Y",$liste[2])." &agrave; ".date("H:i",$liste[2]);
 
	# Couleur de la ligne en fonciton du statut du membre
	if($liste[4]) {
		$select  = '<select name="statut" class="actif">';
		$select .= '<option value="1" selected>'.T_('active').'</option>';
		$select .= '<option value="0">'.T_('inactive').'</option></select>';
		$statut  = T_("active");
	} else {
		$select  = '<select name="statut" class="inactif">';
		$select .= '<option value="0" selected>'.T_('inactive').'</option>';
		$select .= '<option value="1">'.T_('active').'</option></select>';
		$statut  = T_("inactive");
	}

	# Affichage
	$strend = "";
	if (strlen($liste[6])>50)
		$strend = "[...]";


	echo '<form method="POST"><tr>
		<input type="hidden" name="num" value="'.$liste[0].'"/>
		<td class="'.$colore.'" style="width:10%;">'.$liste[1].'</td>
		<td style="width:120px;">'.$date.'</td>
		<td>'.substr($liste[3],0,70).'&nbsp;&nbsp;-&nbsp;&nbsp;<a href="'.$liste[6].'" target="_blank">'.substr($liste[6],0,65).'</a>'.$strend.'</td>
		<td>'.$select.'</td>
		<td style="text-align:center;width:20px;">'.$liste[5].'</td>
		<td  style="width:160px;"><center>
			<input type="submit" class="button br3px" name="submitModif" value="'.T_('Change').'" />
			<input type="submit" class="button br3px" name="submitDelete" value="'.T_('Delete').'" />
			</center>';
	if($num_membre != 0 || $nb_items != 10) {
		echo '<input type="hidden" id="num_membre" name="num_membre" value="'.$num_membre.'" />';
		echo '<input type="hidden" id="nb_items" name="nb_items" value="'.$nb_items.'" /></td>';
		
	}
}
?>
</table>

<?php 
$params = "page=$num_page&";
?>
<div class="nbitems">
<?=T_('Show items by : ');?> <a href="?<?php echo $params; ?>nb_items=10">10</a>, <a href="?<?php echo $params; ?>nb_items=20">20</a>, <a href="?<?php echo $params; ?>nb_items=50">50</a>
</div>
<?php
closeBD();
include(dirname(__FILE__).'/footer.php');
finCache();
?>