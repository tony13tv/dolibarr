<?php
/* Copyright (C) 2013-2016  Olivier Geffroy      <jeff@jeffinfo.com>
 * Copyright (C) 2013-2016  Florian Henry        <florian.henry@open-concept.pro>
 * Copyright (C) 2013-2017  Alexandre Spangaro   <aspangaro@zendsi.com>
 * Copyright (C) 2016-2017  Laurent Destailleur  <eldy@users.sourceforge.net>
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

/**
 * \file        htdocs/accountancy/bookkeeping/fix.php
 * \ingroup        Advanced accountancy
 * \brief        List operation of book keeping
 */
require '../../main.inc.php';
$ressql = $db->query('SELECT import_key FROM llx_accounting_bookkeeping WHERE piece_num = 0');
while ($import_key = $db->fetch_row($ressql)) {
//    var_dump($import_key);
    $ressql = $db->query('SELECT max(piece_num) + 1 FROM llx_accounting_bookkeeping');
    $piece_num = $db->fetch_row($ressql);
    //var_dump($piece_num[0], $import_key[0]);
    if ($import_key !== NULL) {
        $ressql = $db->query("UPDATE llx_accounting_bookkeeping SET piece_num = {$piece_num[0]} WHERE piece_num = 0 AND import_key = {$import_key[0]}");
        //var_dump($ressql);
    }
}
header('Location: /accountancy/bookkeeping/list.php');

