<?php
/**
 * 编辑任务
 *
 * @version        $Id: sys_task_edit.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require(dirname(__FILE__)."/config.php");
CheckPurview('sys_Task');
if(empty($dopost)) $dopost = '';

if($dopost=='save')
{
    $starttime = empty($starttime) ? 0 : GetMkTime($starttime);
    $endtime = empty($endtime) ? 0 : GetMkTime($endtime);
    $runtime = $h.':'.$m;
    $query = "UPDATE `#@__sys_task`
    SET `taskname` = '$taskname',
    `dourl` = '$dourl',
    `islock` = '$nislock',
    `runtype` = '$runtype',
    `runtime` = '$runtime',
    `starttime` = '$starttime',
    `endtime` = '$endtime',
    `freq` = '$freq',
    `description` = '$description',
    `parameter` = '$parameter'
    WHERE id='$id' ";
    $rs = $dsql->ExecuteNoneQuery($query);
    if($rs) 
    {
        ShowMsg('成功修改一个任务!', 'sys_task.php');
    }
    else
    {
        ShowMsg('修改任务失败!'.$dsql->GetError(), 'javascript:;');
    }
    exit();
}

$row = $dsql->GetOne("SELECT * FROM `#@__sys_task` WHERE id='$id' ");
include MiInclude('templets/sys_task_edit.htm');