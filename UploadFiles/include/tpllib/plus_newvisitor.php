<?php   if(!defined('MIINC')) exit("Request Error!");

/**
 * 动态模板memberlist标签
 *
 * @version        $Id: plus_newvisitor.php
 * @package        Mi.Tpllib
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
function plus_newvisitor(&$atts,&$refObj,&$fields)
{
    global $dsql,$_vars,$cfg_memberurl;

    $attlist = "titlelen=30,infolen=200,row=6";
    FillAtts($atts,$attlist);
    FillFields($atts,$fields,$refObj);
    extract($atts, EXTR_OVERWRITE);
    $mid = $_vars['mid'];

    $query = "SELECT h.*,mb.face,mb.sex,mb.userid AS loginid,mb.uname,s.sign FROM `#@__member_vhistory` h
             LEFT JOIN `#@__member` mb ON mb.mid = h.vid
             LEFT JOIN `#@__member_space` s ON s.mid = h.vid
             WHERE  h.mid='$mid' ORDER BY h.vtime DESC LIMIT 0,$row";

    $dsql->SetQuery($query);
    $dsql->Execute("al");
    $rearr = array();
    while($row = $dsql->GetArray("al"))
    {
        $row['url'] = $cfg_memberurl."/index.php?uid=".$row['loginid'];
        if(empty($row['face']))
        {
            $row['face']=($row['sex']=='?')? $cfg_memberurl.'/templets/images/dfgirl.png' : $cfg_memberurl.'/templets/images/dfboy.png';
        }
        $rearr[] = $row;
    }
    $dsql->FreeResult("al");
    return $rearr;
}