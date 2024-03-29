<?php
/**
 * 会员短消息管理
 *
 * @version        $Id: member_msg_edit.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Log');
if(empty($dopost))
{
    ShowMsg("你没指定任何参数！","javascript:;");
    exit();
}
if(empty($dellog)) $dellog = 0;

//删除选定状态
if($dopost=="del")
{
    $bkurl = isset($_COOKIE['ENV_GOBACK_URL']) ? $_COOKIE['ENV_GOBACK_URL'] : "member_msg_main.php";
    $ids = explode('`',$ids);
    $dquery = "";
    foreach($ids as $id)
    {
        if($dquery=="")
        {
            $dquery .= "id='$id' ";
        }
        else
        {
            $dquery .= " OR id='$id' ";
        }
    }
    if($dquery!="") $dquery = " WHERE ".$dquery;
    $dsql->ExecuteNoneQuery("DELETE FROM #@__member_msg $dquery");
    ShowMsg("成功删除指定的记录！",$bkurl);
    exit();
}
//审核选定状态
else if($dopost=="check")
{
    $bkurl = isset($_COOKIE['ENV_GOBACK_URL']) ? $_COOKIE['ENV_GOBACK_URL'] : "member_msg_main.php";
    $ids = explode('`',$ids);
    $dquery = "";
    foreach($ids as $id)
    {
        if($dquery=="") $dquery .= " id='$id' ";
        else $dquery .= " Or id='$id' ";
    }
    if($dquery!="") $dquery = " where ".$dquery;
    $dsql->ExecuteNoneQuery("UPDATE #@__member_msg SET ischeck=1 $dquery");
    ShowMsg("成功审核指定的记录！",$bkurl);
    exit();
}
else
{
    ShowMsg("无法识别你的请求！","javascript:;");
    exit();
}