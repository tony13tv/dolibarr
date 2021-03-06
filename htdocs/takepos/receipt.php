<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier    <jeremie.o@laposte.net>
 * Copyright (C) 2011      Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2012      Marcos García       <marcosgdf@gmail.com>
 * Copyright (C) 2018      Andreu Bisquerra    <jove@bisquerra.com>
 * Copyright (C) 2019      Josep Lluís Amador  <joseplluis@lliuretic.cat>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require '../main.inc.php';	// Load $user and permissions
include_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

$langs->loadLangs(array("main", "cashdesk"));

$place = (GETPOST('place', 'int') > 0 ? GETPOST('place', 'int') : 0);   // $place is id of table for Ba or Restaurant
$posnb = (GETPOST('posnb', 'int') > 0 ? GETPOST('posnb', 'int') : 0);   // $posnb is id of POS

$facid=GETPOST('facid', 'int');


/*
 * View
 */

top_httphead('text/html');

if ($place > 0)
{
    $sql="SELECT rowid FROM ".MAIN_DB_PREFIX."facture where ref='(PROV-POS-".$place.")'";
    $resql = $db->query($sql);
    $obj = $db->fetch_object($resql);
    if ($obj)
    {
        $facid=$obj->rowid;
    }
}
$object=new Facture($db);
$object->fetch($facid);

// IMPORTANT: This file is sended to 'Takepos Printing' application. Keep basic file. No external files as css, js... If you need images use absolute path.
?>
<html>
<body>
<center>
<font size="4">
<?php echo '<b>'.$mysoc->name.'</b>';?>
</font>
</center>
<br>
<p class="left">
<?php
$substitutionarray=getCommonSubstitutionArray($langs);
if (! empty($conf->global->TAKEPOS_HEADER))
{
	$newfreetext=make_substitutions($conf->global->TAKEPOS_HEADER, $substitutionarray);
	echo $newfreetext;
}
?>
</p>
<p class="right">
<?php
print $langs->trans('Date')." ".dol_print_date($object->date, 'day').'<br>';
if ($mysoc->country_code == 'ES') print "Factura simplificada ";
print $object->ref;
?>
</p>
<br>

<table width="100%" style="border-top-style: double;">
    <thead>
	<tr>
        <th class="center"><?php print $langs->trans("Label"); ?></th>
        <th class="right"><?php print $langs->trans("Qty"); ?></th>
        <th class="right"><?php print $langs->trans("Price"); ?></th>
        <th class="right"><?php print $langs->trans("TotalTTC"); ?></th>
	</tr>
    </thead>
    <tbody>
    <?php
    foreach ($object->lines as $line)
    {
    ?>
    <tr>
        <td><?php if (!empty($line->product_label)) echo $line->product_label;
                  else echo $line->description;?>
        </td>
        <td class="right"><?php echo $line->qty;?></td>
        <td class="right"><?php echo $line->total_ttc/$line->qty;?></td>
        <td class="right"><?php echo price($line->total_ttc);?></td>
    </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<br>
<table class="right">
<tr>
    <th class="right"><?php echo $langs->trans("TotalHT");?></th>
    <td class="right"><?php echo price($object->total_ht, 1, '', 1, - 1, - 1, $conf->currency)."\n";?></td>
</tr>
<?php if($conf->global->TAKEPOS_TICKET_VAT_GROUPPED):?>
<?php
	$vat_groups = array();
	foreach ($object->lines as $line)
	{
		if(!array_key_exists($line->tva_tx, $vat_groups)){
			$vat_groups[$line->tva_tx] = 0;
		}
		$vat_groups[$line->tva_tx] += $line->total_tva;
	}
	foreach($vat_groups as $key => $val){
	?>
	<tr>
		<th align="right"><?php echo $langs->trans("VAT").' '.vatrate($key, 1);?></th>
		<td align="right"><?php echo price($val, 1, '', 1, - 1, - 1, $conf->currency)."\n";?></td>
	</tr>
<?php
	}
?>
<?php else: ?>
<tr>
	<th class="right"><?php echo $langs->trans("TotalVAT").'</th><td class="right">'.price($object->total_tva, 1, '', 1, - 1, - 1, $conf->currency)."\n";?></td>
</tr>
<?php endif ?>
<tr>
	<th class="right"><?php echo ''.$langs->trans("TotalTTC").'</th><td class="right">'.price($object->total_ttc, 1, '', 1, - 1, - 1, $conf->currency)."\n";?></td>
</tr>
</table>
<div style="border-top-style: double;">
<br>
<br>
<br>
<?php
$substitutionarray=getCommonSubstitutionArray($langs);
if (! empty($conf->global->TAKEPOS_FOOTER))
{
	$newfreetext=make_substitutions($conf->global->TAKEPOS_FOOTER, $substitutionarray);
	echo $newfreetext;
}
?>

<script type="text/javascript">
    window.print();
</script>
</body>
</html>
