<?php
/**
 * 该页仅用于检测用户登录的情况，如要手工更改系统配置，请更改common.inc.php
 *
 * @version        $Id: config.php
 * @package        Mi.Dialog
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/../common.inc.php");
require_once(dirname(__FILE__)."/../userlogin.class.php");

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$miNowurl   =  '';
$s_scriptName = '';
$isUrlOpen = @ini_get('allow_url_fopen');

$miNowurl = GetCurUrl();
$miNowurls = explode("?",$miNowurl);
$s_scriptName = $miNowurls[0];


//检验用户登录状态
$cuserLogin = new userLogin();

if($cuserLogin->getUserID() <=0 )
{
    if(empty($adminDirHand))
    {
        ShowMsg("<b>提示：需输入后台管理目录才能登录</b><br /><form>请输入后台管理目录名：<input type='hidden' name='gotopage' value='".urlencode($miNowurl)."' /><input type='text' name='adminDirHand' value='admin' style='width:120px;' /><input style='width:80px;' type='submit' name='sbt' value='转入登录' /></form>", "javascript:;");
        exit();
    }
	$adminDirHand = HtmlReplace($adminDirHand, 1);
    $gurl = "../../{$adminDirHand}/login.php?gotopage=".urlencode($miNowurl);
    echo "<script type='text/javascript'>location='$gurl';</script>";
    exit();
}

//启用远程站点则创建FTP类
if($cfg_remote_site=='Y')
{
    require_once(MIINC.'/ftp.class.php');
    if(file_exists(MIDATA."/cache/inc_remote_config.php"))
    {
        require_once MIDATA."/cache/inc_remote_config.php";
    }
    if(empty($remoteuploads)) $remoteuploads = 0;
    if(empty($remoteupUrl)) $remoteupUrl = '';
    //初始化FTP配置
    $ftpconfig = array(
        'hostname'=>$rmhost, 
        'port'=>$rmport,
        'username'=>$rmname,
        'password'=>$rmpwd

    );
    $ftp = new FTP; 
    $ftp->connect($ftpconfig);
}