<?php
/**
 * 系统权限组添加
 *
 * @version        $Id: sys_group_add.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(!empty($dopost))
{
    $row = $dsql->GetOne("SELECT * FROM #@__admintype WHERE rank='".$rankid."'");
    if(is_array($row))
    {
        ShowMsg('你所创建的组别的级别值已存在，不允许重复!', '-1');
        exit();
    }
    if($rankid > 10)
    {
        ShowMsg('组级别值不能大于10， 否则一切权限设置均无效!', '-1');
        exit();
    }
    $AllPurviews = '';
    if(is_array($purviews))
    {
        foreach($purviews as $pur)
        {
            $AllPurviews = $pur.' ';
        }
        $AllPurviews = trim($AllPurviews);
    }
    $dsql->ExecuteNoneQuery("INSERT INTO #@__admintype(rank,typename,system,purviews) VALUES ('$rankid','$groupname', 0, '$AllPurviews');");
    ShowMsg("成功创建一个新的用户组!", "sys_group.php");
    exit();
}
include MiInclude('templets/sys_group_add.htm');