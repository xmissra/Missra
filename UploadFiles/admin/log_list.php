<?php
/**
 * 日志列表
 *
 * @version        $Id: log_list.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Log');
require_once(MIINC."/datalistcp.class.php");
require_once(MIINC."/common.func.php");
setcookie("ENV_GOBACK_URL",$miNowurl,time()+3600,"/");
$sql = $where = "";

if(empty($adminid)) $adminid = 0;
if(empty($cip)) $cip = "";
if(empty($dtime)) $dtime = 0;
if($adminid>0) $where .= " AND #@__log.adminid='$adminid' ";
if($cip!="") $where .= " AND #@__log.cip LIKE '%$cip%' ";

if($dtime>0)
{
    $nowtime = time();
    $starttime = $nowtime - ($dtime*24*3600);
    $where .= " AND #@__log.dtime>'$starttime' ";
}
$sql = "SELECT #@__log.*,#@__admin.userid FROM #@__log
     LEFT JOIN #@__admin ON #@__admin.id=#@__log.adminid
     WHERE 1=1 $where ORDER BY #@__log.lid DESC";
$adminlist = "";
$dsql->SetQuery("SELECT id,uname FROM #@__admin");
$dsql->Execute('admin');
while($myrow = $dsql->GetObject('admin'))
{
    $adminlist .="<option value='{$myrow->id}'>{$myrow->uname}</option>\r\n";
}
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("adminid",$adminid);
$dlist->SetParameter("cip",$cip);
$dlist->SetParameter("dtime",$dtime);
$dlist->SetTemplate(MIADMIN."/templets/log_list.htm");
$dlist->SetSource($sql);
$dlist->Display();