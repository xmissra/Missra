<?php
if(!defined('MIINC'))
{
    exit("Request Error!");
}
require_once(MIINC."/taglib/flink.lib.php");
/**
 * 友情链接
 *
 * @version        $Id: flinktype.lib.php
 * @package        Missra.Taglib
 * @copyright      Copyright (c) Missra, Inc.
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */

/*>>missra>>
<name>友情链接类型</name>
<type>全局标记</type>
<for>V1.x</for>
<description>用于获取友情链接类型</description>
<demo>
{missra:flink row='24'/}
</demo>
<attributes>
    <iterm>row:链接类型数量</iterm>
    <iterm>titlelen:链接文字的长度</iterm>
</attributes> 
>>missra>>*/
 
function lib_flinktype(&$ctag,&$refObj) {
    global $dsql;
    $attlist="row|24,titlelen|24";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);

    $totalrow = $row;
    $revalue = '';
  
    $equery = "SELECT * FROM #@__flinktype order by id asc limit 0,$totalrow";

    if(trim($ctag->GetInnerText())=='') $innertext = "<li>[field:typename /]</li>";
    else $innertext = $ctag->GetInnerText();
	if(!isset($type)) $type = '';
    $dtp = new MiTagParse();
    $dtp->SetNameSpace("missra","{","}");
    $dtp->LoadString($innertext);
    
    $dsql->SetQuery($equery);
    $dsql->Execute();
    $rs = '';
    $row = array();
    while($dbrow=$dsql->GetObject()) {
        $row[] = $dbrow;
    }
	$missra = false;
	$missra->id = 999;
	$missra->typename = '玫莎链';
	if($type == 'missra') $row[] = $missra;
	
    foreach ($row as $key => $value) {
        if (is_array($dtp->CTags)) {
            $GLOBALS['envs']['flinkid'] = $value->id;
            foreach($dtp->CTags as $tagid=>$ctag) {
                $tagname = $ctag->GetName();
                if($tagname=="flink") $dtp->Assign($tagid, lib_flink($ctag, $refObj));
            }
        }
        $rs = $dtp->GetResult();
    	$rs = preg_replace("/\[field:id([\/\s]{0,})\]/isU", $value->id, $rs);
        $rs = preg_replace("/\[field:typename([\/\s]{0,})\]/isU", $value->typename, $rs);
        $revalue .= $rs;
    }
    
    return $revalue;
}