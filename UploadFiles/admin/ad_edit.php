<?php
/**
 * 广告编辑
 *
 * @version        $Id: ad_edit.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_广告管理');
require_once(MIINC.'/typelink.class.php');
if(empty($dopost)) $dopost = '';
$aid = preg_replace("#[^0-9]#", '', $aid);
$ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? "ad_main.php" : $_COOKIE['ENV_GOBACK_URL'];

if($dopost=='delete')
{
    $dsql->ExecuteNoneQuery("DELETE FROM `#@__myad` WHERE aid='$aid' ");
    ShowMsg("成功删除一则广告代码！",$ENV_GOBACK_URL);
    exit();
}
else if($dopost=="gettag")
{
    require_once(MIINC.'/oxwindow.class.php');
    $jscode = "<script src='{$cfg_phpurl}/ad_js.php?aid=$aid' type='text/javascript'></script>";
    $showhtml = "<xmp style='color:#333333;background-color:#ffffff'>\r\n\r\n$jscode\r\n\r\n</xmp>";
    $showhtml .= "预览：<iframe name='testfrm' frameborder='0' src='ad_edit.php?aid={$aid}&dopost=testjs' id='testfrm' width='100%' height='200'></iframe>";
    $row = $dsql->GetOne("SELECT tagname from `#@__myad` WHERE aid='$aid' ");
    
    $showtag = '{'."missra:myad name='{$row['tagname']}'/".'}';
    $info = "<strong>说明：</strong>如果嵌入的是玫莎CMS广告标签，那么将会解析成标签中的内容到页面，广告更改后需要重新生成。如果不希望重新生成所有页面，则直接调用JS代码即可。";
    $wintitle = "广告管理-获取广告标签";
    $wecome_info = "<a href='ad_main.php'>广告管理</a>::获取JS";
    $win = new OxWindow();
    $win->Init();
    $winform = $win->GetWindow("hand",$info);
    $win->AddTitle("玫莎CMS标签调用代码：");
    $winform = $win->GetWindow("hand",$showtag);
    $win->myWinItem = '';
    $win->AddTitle("以下为选定广告的JS调用代码：");
    $winform = $win->GetWindow("hand",$showhtml);
    $win->Display();
    exit();
}
else if($dopost=='testjs')
{
    echo "<script src='{$cfg_phpurl}/ad_js.php?aid=$aid&nocache=1' type='text/javascript'></script>";
    exit();
}
else if($dopost=='saveedit')
{
    $starttime = GetMkTime($starttime);
    $endtime = GetMkTime($endtime);
    $query = "UPDATE `#@__myad`
     SET
     clsid='$clsid',
     typeid='$typeid',
     adname='$adname',
     timeset='$timeset',
     starttime='$starttime',
     endtime='$endtime',
     normbody='$normbody',
     expbody='$expbody'
     WHERE aid='$aid'
     ";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功更改一则广告代码！",$ENV_GOBACK_URL);
    exit();
}

$row = $dsql->GetOne("SELECT * FROM `#@__myad` WHERE aid='$aid'");
$dsql->Execute('dd','SELECT * FROM `#@__myadtype` ORDER BY id DESC');
$option = '';
while($arr = $dsql->GetArray('dd')) {
    if ($arr['id'] == $row['clsid'])
    {
        $option .= "<option value='{$arr['id']}' selected='selected'>{$arr['typename']}</option>\n\r";
    } else {
        $option .= "<option value='{$arr['id']}'>{$arr['typename']}</option>\n\r";
    }
}
include MiInclude('templets/ad_edit.htm');