<?php
/**
 *
 * 信息推荐
 *
 * @version        $Id: recommend.php
 * @package        Mi.Site
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(MIINC."/channelunit.class.php");
if(!isset($action)) $action = '';

if(isset($arcID)) $aid = $arcID;
$arcID = $aid = (isset($aid) && is_numeric($aid) ? $aid : 0);
$type = (!isset($type) ? "" : $type);

if(empty($aid)) {
    ShowMsg("文档ID不能为空!","-1");
    exit();
}

//读取文档信息
if($action=='')
{
    if($type=='sys'){
    //读取文档信息
        $arcRow = GetOneArchive($aid);
        if($arcRow['aid']=='') 
        {
            ShowMsg("无法把未知文档推荐给好友!","-1");
            exit();
        }
        extract($arcRow, EXTR_OVERWRITE);
    } else {
        $arcRow=$dsql->GetOne("SELECT s.*,t.* FROM `#@__member_stow` AS s LEFT JOIN `#@__member_stowtype` AS t ON s.type=t.stowname WHERE s.aid='$aid' AND s.type='$type'");
        if(!is_array($arcRow)){
            ShowMsg("无法把未知文档推荐给好友!","-1");
            exit();
        }
        $arcRow['arcurl']=$arcRow['indexurl']."=".$arcRow['aid'];
        extract($arcRow, EXTR_OVERWRITE);
    }
}

//发送推荐信息
else if($action=='send')
{
    if(!CheckEmail($email))
    {
        echo "<script>alert('Email格式不正确!');history.go(-1);</script>";
        exit();
    }
    $mailbody = '';
    $msg = mi_htmlspecialchars($msg);
    $mailtitle = "你的好友给你推荐了一篇文章";
    $mailbody .= "$msg \r\n\r\n";
    $mailbody .= "Power by http://www.missra.com Missra内容管理系统！";

    $headers = "From: ".$cfg_adminemail."\r\nReply-To: ".$cfg_adminemail;
    
    if($cfg_sendmail_bysmtp == 'Y' && !empty($cfg_smtp_server))
    {        
        $mailtype = 'TXT';
        require_once(MIINC.'/mail.class.php');
        $smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
        $smtp->debug = false;
        $smtp->sendmail($email,$cfg_webname,$cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
    }
    else
    {
        @mail($email, $mailtitle, $mailbody, $headers);
    }

    ShowMsg("成功推荐一篇文章!",$arcurl);
    exit();
}
$arcRow = GetOneArchive($aid);
if($arcRow['aid']=='') 
{
    ShowMsg("无法把未知文档推荐给好友!","-1");
    exit();
}
extract($arcRow, EXTR_OVERWRITE);
//显示模板(简单PHP文件)
include(MITEMPLATE.'/plus/recommend.htm');