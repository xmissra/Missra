<?php
/**
 * 友情链接类型
 *
 * @version        $Id: friendlink_type.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = '';

//保存更改
if($dopost=="save")
{
    $startID = 1;
    $endID = $idend;
    for(;$startID<=$endID;$startID++)
    {
        $query = '';
        $tid = ${'ID_'.$startID};
        $pname =   ${'pname_'.$startID};
        if(isset(${'check_'.$startID}))
        {
            if($pname!='')
            {
                $query = "UPDATE `#@__myadtypee` SET typename='$pname' WHERE id='$tid' ";
                $dsql->ExecuteNoneQuery($query);
            }
        }
        else
        {
            $query = "DELETE FROM `#@__myadtype` WHERE id='$tid' ";
            $dsql->ExecuteNoneQuery($query);
        }
    }
    //增加新记录
    if(isset($check_new) && $pname_new!='')
    {
        $query = "INSERT INTO `#@__myadtype`(typename) VALUES('{$pname_new}');";
        $dsql->ExecuteNoneQuery($query);
    }
    header("Content-Type: text/html; charset={$cfg_soft_lang}");
    ShowMsg("成功更新广告分类列表！", 'adtype_main.php');
    exit;
}

include MiInclude('templets/adtype_main.htm');