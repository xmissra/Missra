<?php
/**
 * 视频发布
 *
 * @version        $Id: vedio_add.php
 * @package        Mi.Administrator
 * @copyright      Copyright (c)  2010, Missra, Inc.
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('a_New,a_AccNew');
require_once(MIINC."/customfields.func.php");
require_once(MIADMIN."/inc/inc_archives_functions.php");
if(file_exists(MIDATA.'/template.rand.php')) {
	require_once(MIDATA.'/template.rand.php');
}
if(empty($dopost)) {
	$dopost = '';
}
if($dopost!='save') {
	require_once(MIINC."/mitag.class.php");
	require_once(MIADMIN."/inc/inc_catalog_options.php");
	$channelid = empty($channelid) ? 0 : intval($channelid);
	$cid = empty($cid) ? 0 : intval($cid);

	//获得频道模型ID
	if($cid>0 && $channelid==0) {
		$row = $dsql->GetOne("Select channeltype From `#@__arctype` where id='$cid'; ");
		$channelid = $row['channeltype'];
	} else {
		if($channelid==0) {
			$channelid = 1;
		}
	}



	//获得频道模型信息
	$cInfos = $dsql->GetOne(" Select * From  `#@__channeltype` where id='$channelid' ");
	include MiInclude("templets/video_add.htm");
	exit();
}
/*--------------------------------
function __save(){  }
-------------------------------*/
else if($dopost=='save') {
	require_once(MIINC.'/image.func.php');
	require_once(MIINC.'/oxwindow.class.php');
	
	$flag = isset($flags) ? join(',',$flags) : '';
	if(!isset($typeid2)) $typeid2 = 0;
	if(!isset($autokey)) $autokey = 0;
	if(!isset($remote)) $remote = 0;
	if(!isset($dellink)) $dellink = 0;
	if(!isset($autolitpic)) $autolitpic = 0;

	if($typeid==0) {
		ShowMsg("请指定文档的栏目！","-1");
		exit();
	}
	
	if(empty($channelid)) {
		ShowMsg("文档为非指定的类型，请检查你发布内容的表单是否合法！","-1");
		exit();
	}
	if(!CheckChannel($typeid,$channelid) ) {
		ShowMsg("你所选择的栏目与当前模型不相符，请选择白色的选项！","-1");
		exit();
	}
	
	if(!TestPurview('a_New')) {
		CheckCatalog($typeid,"对不起，你没有操作栏目 {$typeid} 的权限！");
	}

	//对保存的内容进行处理
	if(empty($writer)) $writer=$cuserLogin->getUserName();
	if(empty($source)) $source='未知';
	$pubdate = GetMkTime($pubdate);
	$senddate = time();
	$sortrank = AddDay($pubdate,$sortup);
	if($ishtml==0) {
		$ismake = -1;
	} else {
		$ismake = 0;
	}
	$title = cn_substrR($title,$cfg_title_maxlen);
	$shorttitle = cn_substrR($shorttitle,36);
	$color =  cn_substrR($color,7);
	$writer =  cn_substrR($writer,20);
	$source = cn_substrR($source,30);
	$description = cn_substrR($description,250);
	$keywords = cn_substrR($keywords,30);
	$filename = trim(cn_substrR($filename,40));
	$userip = GetIP();
	if(!TestPurview('a_Check,a_AccCheck,a_MyCheck')) {
		$arcrank = -1;
	}
	$adminid = $cuserLogin->getUserID();

	//处理上传的缩略图
	if(empty($ddisremote)) {
		$ddisremote = 0;
	}
	
	$litpic = GetDDImage('$litpic', $picname, $ddisremote);

	//生成文档ID
	$arcID = GetIndexKey($arcrank,$typeid,$sortrank,$channelid,$senddate,$adminid);
    
	if(empty($arcID)) {
		ShowMsg("无法获得主键，因此无法进行后续操作！","-1");
		exit();
	}
	if(trim($title) == '') {
		ShowMsg('标题不能为空', '-1');
		exit();
	}




//附加表信息
//视频列表
$videolist='';
for($i=1;$i<=count($videoname);$i++){
	if(empty($videoname[$i])) continue ;
	if(empty($videourl[$i])) continue ;
	$videolist .= "$videoname[$i]{span}$videourl[$i]{li}";
}
//剧情介绍
//处理plot字段自动摘要、自动提取缩略图等
$plot = AnalyseHtmlBody($plot,$description,$litpic,$keywords,'htmltext');
//播放器类型
if(!isset($players)) $players = 0;

	//处理图片文档的自定义属性
	if($litpic!='' && !ereg('p',$flag)) {
		$flag = ($flag=='' ? 'p' : $flag.',p');
	}
	
	if($redirecturl!='' && !ereg('j',$flag)) {
		$flag = ($flag=='' ? 'j' : $flag.',j');
	}

	//保存到主表
	$inQuery = "INSERT INTO `#@__archives`(id,typeid,typeid2,sortrank,flag,ismake,channel,arcrank,click,money,title,shorttitle,color,writer,source,litpic,pubdate,senddate,mid,description,keywords,filename)
    VALUES ('$arcID','$typeid','$typeid2','$sortrank','$flag','$ismake','$channelid','$arcrank','0','$money','$title','$shorttitle','$color','$writer','$source','$litpic','$pubdate','$senddate','$adminid','$description','$keywords','$filename');";
	if(!$dsql->ExecuteNoneQuery($inQuery)) {
		$gerr = $dsql->GetError();
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
		ShowMsg("把数据保存到数据库主表 `#@__archives` 时出错，请把相关信息提交给Missra官方。".str_replace('"','',$gerr),"javascript:;");
		exit();
	}



	//保存到附加表
	$cts = $dsql->GetOne("Select addtable From `#@__channeltype` where id='$channelid' ");
	$addtable = trim($cts['addtable']);
	if(empty($addtable)) {
		$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
		ShowMsg("没找到当前模型[{$channelid}]的主表信息，无法完成操作！。","javascript:;");
		exit();
	}
	$daccess = isset($daccess) && is_numeric($daccess) ? $daccess : 0;
	$useip = GetIP();
	$inQuery = "INSERT INTO `$addtable`(aid,typeid,redirecturl,userip,videolist,plot,players,language,softrank)
    VALUES ('$arcID','$typeid','$redirecturl','$useip','$videolist','$plot','$players','$language','$softrank');";
	if(!$dsql->ExecuteNoneQuery($inQuery)) {
		$gerr = $dsql->GetError();
		$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
		ShowMsg("把数据保存到数据库附加表 `{$addtable}` 时出错，请把相关信息提交给Missra官方。".str_replace('"','',$gerr),"javascript:;");
		exit();
	}

	//生成HTML
	InsertTags($tags,$arcID);
	$ID=$arcID;
	$arcUrl = MakeArt($arcID,true,true);
	if($arcUrl=="") {
		$arcUrl = $cfg_phpurl."/view.php?aid=$arcID";
	}

	//返回成功信息
	$msg = "
    　　请选择你的后续操作：
		<a href='video_add.php?cid=$typeid'>继续发布视频</a>&nbsp;&nbsp;
		<a href='$arcUrl' target='_blank'>查看视频</a>&nbsp;&nbsp;
		<a href='archives_do.php?aid=".$arcID."&dopost=editArchives'>更改视频</a>&nbsp;&nbsp;
		<a href='catalog_do.php?cid=$typeid&dopost=listArchives'>已发布视频管理</a>&nbsp;&nbsp;
		<a href='catalog_main.php'>网站栏目管理</a>";

	$wintitle = "成功发布一个视频！";
	$wecome_info = "视频管理::发布视频";
	$win = new OxWindow();
	$win->AddTitle("成功发布视频：");
	$win->AddMsgItem($msg);
	$winform = $win->GetWindow("hand","&nbsp;",false);
	$win->Display();
}

?>