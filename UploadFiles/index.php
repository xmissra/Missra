<?php
/**
 * @version        $Id: index.php
 * @package        Mi.Site
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/license.html
 * @link           http://www.missra.com
 */
if(!file_exists(dirname(__FILE__).'/data/common.inc.php'))
{
    header('Location:install/index.php');
    exit();
}
//自动生成HTML版
if(isset($_GET['upcache']) || !file_exists('index.html'))
{
    require_once (dirname(__FILE__) . "/include/common.inc.php");
    require_once MIINC."/arc.partview.class.php";
    $GLOBALS['_arclistEnv'] = 'index';
    $row = $dsql->GetOne("Select * From `#@__homepageset`");
    $row['templet'] = MfTemplet($row['templet']);
    $pv = new PartView();
    $pv->SetTemplet($cfg_basedir . $cfg_templets_dir . "/" . $row['templet']);
    $row['showmod'] = isset($row['showmod'])? $row['showmod'] : 0;
    if ($row['showmod'] == 1)
    {
        $pv->SaveToHtml(dirname(__FILE__).'/index.html');
        include(dirname(__FILE__).'/index.html');
        exit();
    } else { 
        $pv->Display();
        exit();
    }
}
else
{
    header('HTTP/1.1 301 Moved Permanently');
    header('Location:index.html');
}
?>