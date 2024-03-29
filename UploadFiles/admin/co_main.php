<?php
/**
 * 采集规则管理
 *
 * @version        $Id: co_main.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/config.php");
require_once(MIINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL", $miNowurl, time()+3600, "/");
$sql  = "SELECT co.nid,co.channelid,co.notename,co.sourcelang,co.uptime,co.cotime,co.pnum,ch.typename";
$sql .= " FROM `#@__co_note` co LEFT JOIN `#@__channeltype` ch ON ch.id=co.channelid ORDER BY co.nid DESC";
$dlist = new DataListCP();
$dlist->SetTemplet(MIADMIN."/templets/co_main.htm");
$dlist->SetSource($sql);
$dlist->display();

function GetDatePage($mktime)
{
    return $mktime=='0' ? '从未采集过' : MyDate('Y-m-d',$mktime);
}

function TjUrlNum($nid)
{
    global $dsql;
    $row = $dsql->GetOne("SELECT COUNT(*) AS dd FROM `#@__co_htmls` WHERE nid='$nid' ");
    return $row['dd'];
}