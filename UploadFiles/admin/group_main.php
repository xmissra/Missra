<?php
/**
 *  圈子列表
 *
 * @version        $Id: group_main.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('group_Main');
require_once(MIINC.'/datalistcp.class.php');
setcookie("ENV_GOBACK_URL",$miNowurl,time()+3600,"/");

$id = isset($id) && is_numeric($id) ? $id : 0;
$action = isset($action) ? trim($action) : '';

$keyword = isset($keyword) ? trim($keyword) : '';
$keyword = stripslashes($keyword);
$keyword = preg_replace("#[\"\r\n\t\*\?\(\)\$%']#"," ",trim($keyword));
$keyword = addslashes($keyword);

if($action=="close")
{
    if($id > 0)
    {
        $db->ExecuteNoneQuery("UPDATE #@__groups SET ishidden=1 WHERE groupid='$id'");
    }
}
else if($action=="open")
{
    if($id > 0)
    {
        $db->ExecuteNoneQuery("UPDATE #@__groups SET ishidden=0 WHERE groupid='$id'");
    }
}
else if($action=="del")
{
    if($id > 0)
    {
        $db->ExecuteNoneQuery("DELETE FROM #@__groups WHERE groupid='$id'");
        $db->ExecuteNoneQuery("DELETE FROM #@__group_threads WHERE gid='$id'");
        $db->ExecuteNoneQuery("DELETE FROM #@__group_posts WHERE gid='$id'");
    }
}

$db->SetQuery("SELECT * FROM #@__store_groups WHERE tops=0 ORDER BY orders ASC");
$db->Execute(1);
$option = '';
while($rs = $db->GetArray(1))
{
    $option .= "<option value='".$rs['storeid']."'>".$rs['storename']."</option>\n";
    $v = $rs['storeid'];
    $db->SetQuery("SELECT * FROM #@__store_groups WHERE tops='{$v}' ORDER BY orders ASC");
    $db->Execute(2);
    while($rs = $db->GetArray(2))
    {
        $option .= "<option value='".$rs['storeid']."'>--".$rs['storename']."</option>\n";
    }
}

$wheresql = "WHERE groupid>0";

if(!empty($keyword))
{
    $wheresql .= " AND (groupname like '%".$keyword."%' OR des like '%".$keyword."%' OR creater like '%".$keyword."%')";
}
if(!isset($username))
{
    $username = '';
}
if(!empty($username))
{
    $wheresql .= " AND creater like '%".$username."%'";
}
if(!isset($store))
{
    $store = -1;
}
if($store > 0)
{
    $wheresql .= " AND (storeid='".$store."' OR rootstoreid='".$store."')";
}
$sql = "SELECT * FROM #@__groups $wheresql ORDER BY stime DESC";

$dl = new DataListCP();
$dl->pageSize = 20;
$dl->SetParameter("username",$username);
$dl->SetParameter("store",$store);
$dl->SetParameter("keyword",$keyword);

//这两句的顺序不能更换
$dl->SetTemplate(MIADMIN."/templets/group_main.htm");      //载入模板
$dl->SetSource($sql);            //设定查询SQL
$dl->Display();                  //显示

function GetGroupstore($id)
{
    global $db;
    $row = $db->GetOne("SELECT storename,tops FROM #@__store_groups WHERE storeid='{$id}'");
    if(is_array($row))
    {
        $store = $row['storename'];
        if(!$row['tops'])
        {
            return $row['storename'];
        }
        else
        {
            $rs = $db->GetOne("SELECT storename FROM #@__store_groups WHERE storeid='".$row['tops']."'");
            return $rs['storename'].">".$store;
        }
    }
    else
    {
        return false;
    }
}

?>