<?php   if(!defined('MIINC')) exit('Request Error!');
/**
 * 栏目单元,主要用户管理后台管理处
 *
 * @version        $Id: typeunit.class.admin
 * @package        Missra.Libraries
 * @copyright      Copyright (c)  2010, Missra, Inc.
 * @link           http://www.missra.com
 */
 
require_once(MIINC."/channelunit.func.php");

/**
 * 栏目单元,主要用户管理后台管理处
 *
 * @package          TypeUnit
 * @subpackage       Mi.Libraries
 * @link             http://www.missra.com
 */
class TypeUnit {
    var $dsql;
    var $artDir;
    var $baseDir;
    var $idCounter;
    var $idArrary;
    var $shortName;
    var $CatalogNums;

    //php5构造函数
    function __construct() {
        $this->idCounter = 0;
        $this->artDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
        $this->baseDir = $GLOBALS['cfg_basedir'];
        $this->shortName = $GLOBALS['art_shortname'];
        $this->idArrary = '';
        $this->dsql = 0;
    }

    function TypeUnit() {
        $this->__construct();
    }

    //清理类
    function Close() {
    }

    //获取所有栏目的文档ID数
    function UpdateCatalogNum() {
        $this->dsql->SetQuery("SELECT typeid,count(typeid) as dd FROM `#@__arctiny` WHERE arcrank <>-2 group by typeid");
        $this->dsql->Execute();
        while($row = $this->dsql->GetArray())
        {
            $this->CatalogNums[$row['typeid']] = $row['dd'];
        }
    }

    function GetTotalArc($tid) {
        if(!is_array($this->CatalogNums)) {
            $this->UpdateCatalogNum();
        }
        if(!isset($this->CatalogNums[$tid])) {
            return 0;
        } else {
            $totalnum = 0;
            $ids = explode(',',GetSonIds($tid));
            foreach($ids as $tid) {
                if(isset($this->CatalogNums[$tid])) {
                    $totalnum += $this->CatalogNums[$tid];
                }
            }
            return $totalnum;
        }
    }

    /**
     *  读出所有分类,在类目管理页(list_type)中使用
     *
     * @access    public
     * @param     int   $channel  频道ID
     * @param     int   $nowdir  当前操作ID
     * @return    string
     */
    function ListAllType($channel=0,$nowdir=0) {
        global $cfg_admin_channel, $admin_catalogs;
        $this->dsql = $GLOBALS['dsql'];
        
        //检测用户有权限的顶级栏目
        if($cfg_admin_channel=='array') {
            $admin_catalog = join(',', $admin_catalogs);
            $this->dsql->SetQuery("SELECT reid FROM `#@__arctype` WHERE id in($admin_catalog) group by reid ");
            $this->dsql->Execute();
            $topidstr = '';
            while($row = $this->dsql->GetObject()) {
                if($row->reid==0) continue;
                $topidstr .= ($topidstr=='' ? $row->reid : ','.$row->reid);
            }
            $admin_catalog .= ','.$topidstr;
            $admin_catalogs = explode(',', $admin_catalog);
            $admin_catalogs = array_unique($admin_catalogs);
        }

        $this->dsql->SetQuery("SELECT id,typedir,typename,ispart,sortrank,ishidden FROM `#@__arctype` WHERE reid=0 order by sortrank");
        $this->dsql->Execute(0);
        while($row = $this->dsql->GetObject(0)) {
            if( $cfg_admin_channel=='array' && !in_array($row->id, $admin_catalogs) ) {
                continue;
            }
            $typeDir = $row->typedir;
            $typeName = $row->typename;
            $ispart = $row->ispart;
            $id = $row->id;
            $rank = $row->sortrank;
            if($row->ishidden=='1') {
                $nss = "<font color='red'>[隐]</font>";
            } else {
                $nss = '';
            }
            echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\r\n";
            //普通列表
            if($ispart==0) {
                echo "<tr onMouseMove=\"javascript:this.bgColor='#F5F5F5';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">";
                if(isset($GLOBALS['exallct'])){
                    echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/micontract.gif' width='11' height='11'></td>\r\n";
                } else {
                    echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/miexplode.gif' width='11' height='11'></td>\r\n";
                }
                echo "<td width='2%' class='bline' align='center'><input class='np' type='checkbox' name='tids[]' value='{$id}'></td>
                    <td width='150' class='bline'><a href='catalog_do.php?cid=".$id."&dopost=listArchives' oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\">{$nss}".$typeName."</a> <a onclick=\"AlertMsg('快捷编辑窗口','$id');\" href=\"javascript:;\"><img src='images/write2.gif'/></a></td>
                    <td width='70' class='bline'>ID：".$id."</td>
                    <td width='10%' class='bline'>文档数：".$this->GetTotalArc($id)."</td>
                    <td align='right' class='bline'>
                        <a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a> | 
                        <a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a> | 
                        <a href='catalog_add.php?id={$id}'>增加子类</a> | 
                        <a href='catalog_edit.php?id={$id}'>更改</a> | 
                        <a href='catalog_do.php?dopost=moveCatalog&typeid={$id}'>移动</a> | 
                        <a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>&nbsp;
                        <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25px;height:20px'>
                    </td>
                </tr>";
            }
            //带封面的频道
            else if($ispart==1) {
                echo "<tr onMouseMove=\"javascript:this.bgColor='#F5F5F5';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">";
                if(isset($GLOBALS['exallct'])){
                    echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/micontract.gif' width='11' height='11'></td>\r\n";
                } else {
                    echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/miexplode.gif' width='11' height='11'></td>\r\n";
                }
                
                echo "<td width='2%' class='bline' align='center'><input class='np' type='checkbox' name='tids[]' value='{$id}'></td>
                    <td width='150' class='bline'><a href='catalog_do.php?cid=".$id."&dopost=listArchives' oncontextmenu=\"CommonMenuPart(event,this,$id,'".urlencode($typeName)."')\">{$nss}".$typeName."</a>  <a onclick=\"AlertMsg('快捷编辑窗口','$id');\" href=\"javascript:;\"> <img src='images/write2.gif'/> </a></td>
                    <td width='70' class='bline'>ID：".$id."</td>
                    <td width='10%' class='bline'>封面频道</td>
                    <td align='right' class='bline'>
                        <a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a> | 
                        <a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a> | 
                        <a href='catalog_add.php?id={$id}'>增加子类</a> | 
                        <a href='catalog_edit.php?id={$id}'>更改</a> | 
                        <a href='catalog_do.php?dopost=moveCatalog&typeid={$id}'>移动</a> | 
                        <a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>&nbsp;
                        <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25px;height:20px'>
                    </td>
                </tr>";
            }
            //独立页面
            else if($ispart==2) {
                echo "<tr onMouseMove=\"javascript:this.bgColor='#F5F5F5';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">";
                if(isset($GLOBALS['exallct'])){
                    echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/micontract.gif' width='11' height='11'></td>\r\n";
                } else {
                    echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/miexplode.gif' width='11' height='11'></td>\r\n";
                }
                
                echo "<td width='2%' class='bline' align='center'><input class='np' type='checkbox' name='tids[]' value='{$id}'></td>
                    <td width='150' class='bline'><a href='catalog_edit.php?id=".$id."' oncontextmenu=\"SingleMenu(event,this,$id,'".urlencode($typeName)."')\">{$nss}".$typeName."</a>  <a onclick=\"AlertMsg('快捷编辑窗口','$id');\" href=\"javascript:;\"><img src='images/write2.gif'/></a></td>
                    <td width='70' class='bline'>ID：".$id."</td>
                    <td width='10%' class='bline'>外链页面</td>
                    <td align='right' class='bline'>
                        <a href='{$typeDir}' target='_blank'>预览</a> |
                        <a href='catalog_edit.php?id={$id}'>更改</a> |
                        <a href='catalog_do.php?dopost=moveCatalog&typeid={$id}'>移动</a> |
                        <a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>&nbsp;
                        <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25px;height:20px'>
                    </td>
                </tr>";
            }
            echo "<tr><td colspan='6' id='suns".$id."' class='sub-item'>";
            //$lastid = GetCookie('lastCid');
            //if($channel==$id /*|| $lastid==$id*/ || isset($GLOBALS['exallct']) || $cfg_admin_channel=='array') {
            if($channel==$id || isset($GLOBALS['exallct']) || $cfg_admin_channel=='array') {    
                echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\r\n";
                $this->LogicListAllSunType($id,"　");
                echo "</table>\r\n";
            }
            echo "</td></tr>\r\n</table>\r\n";
        }
    }

    /**
     *  获得子类目的递归调用
     *
     * @access    public
     * @param     int  $id  栏目ID
     * @param     string  $step  层级标志
     * @return    void
     */
    function LogicListAllSunType($id, $step) {
        global $cfg_admin_channel, $admin_catalogs;
        $fid = $id;
        $this->dsql->SetQuery("SELECT id,reid,typedir,typename,ispart,sortrank,ishidden FROM `#@__arctype` WHERE reid='".$id."' order by sortrank");
        $this->dsql->Execute($fid);
        if($this->dsql->GetTotalRow($fid)>0) {
            while($row = $this->dsql->GetObject($fid)) {
                if($cfg_admin_channel=='array' && !in_array($row->id, $admin_catalogs) ) {
                    continue;
                }
                $typeDir = $row->typedir;
                $typeName = $row->typename;
                $reid = $row->reid;
                $id = $row->id;
                $ispart = $row->ispart;
                if($step==" ") {
                    $stepdd = 1;
                } else {
                    $stepdd = 2;
                }
                $rank = $row->sortrank;
                if($row->ishidden=='1') {
                    $nss = "<font color='red'>[隐]</font>";
                } else {
                    $nss = '';
                }

                //普通列表
                if($ispart==0) {
                    echo "<tr height='26' oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\" onMouseMove=\"javascript:this.bgColor='#F5F5F5';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">";
                    if(isset($GLOBALS['exallct'])) {
                        echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/micontract.gif' width='11' height='11'></td>";
                    } else {
                        echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/miexplode.gif' width='11' height='11'></td>";
                    }
                    echo "<td width='2%' class='bline' align='center'><input class='np' type='checkbox' name='tids[]' value='{$id}'></td>
                        <td width='150' class='bline'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>{$nss}".$typeName."</a>$step<a onclick=\"AlertMsg('快捷编辑窗口','$id');\" href=\"javascript:;\"><img src='images/write2.gif'/></a></td>
                        <td width='70' class='bline'>ID：".$id."</td>
                        <td width='10%' class='bline'>文档数：".$this->GetTotalArc($id)."</td>
                        <td align='right' class='bline'>
                            <a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a> | 
                            <a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a> | 
                            <a href='catalog_add.php?id={$id}'>增加子类</a> | 
                            <a href='catalog_edit.php?id={$id}'>更改</a> | 
                            <a href='catalog_do.php?dopost=moveCatalog&typeid={$id}'>移动</a> | 
                            <a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>&nbsp; 
                            <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25px;height:20px'>
                        </td>
                    </tr>";
                }

                //封面频道
                else if($ispart==1) {
                    echo "<tr height='26' oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\" onMouseMove=\"javascript:this.bgColor='#F5F5F5';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">";
                    if(isset($GLOBALS['exallct'])){
                        echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/micontract.gif' width='11' height='11'></td>";
                    } else {
                        echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/miexplode.gif' width='11' height='11'></td>";
                    }
                    
                    echo "<td width='2%' class='bline' align='center'><input class='np' type='checkbox' name='tids[]' value='{$id}'></td>
                        <td width='150' class='bline'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>{$nss}".$typeName."</a><a onclick=\"AlertMsg('快捷编辑窗口','$id');\" href=\"javascript:;\"><img src='images/write2.gif'/></a></td>
                        <td width='70' class='bline'>ID：".$id."</td>
                        <td width='10%' class='bline'>封面频道</td>
                        <td align='right' class='bline'>
                            <a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a> | 
                            <a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a> | 
                            <a href='catalog_add.php?id={$id}'>增加子类</a> | 
                            <a href='catalog_edit.php?id={$id}'>更改</a> | 
                            <a href='catalog_do.php?dopost=moveCatalog&typeid={$id}'>移动</a> | 
                            <a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>&nbsp; 
                            <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25px;height:20px'>
                        </td>
                    </tr>";
                }

                //独立页面
                else if($ispart==2) {
                    echo "<tr height='26' oncontextmenu=\"SingleMenu(event,this,$id,'".urlencode($typeName)."')\" onMouseMove=\"javascript:this.bgColor='#F5F5F5';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">";
                    if(isset($GLOBALS['exallct'])){
                        echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/micontract.gif' width='11' height='11'></td>";
                    } else {
                        echo "<td width='2%' height='30' class='bline' align='center'><img style='cursor:pointer' id='img".$id."' onClick=\"LoadSuns('suns".$id."',$id);\" src='images/miexplode.gif' width='11' height='11'></td>";
                    }
                    
                    echo "<td width='2%' class='bline' align='center'><input class='np' type='checkbox' name='tids[]' value='{$id}'></td>
                    <td width='150' class='bline'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>{$nss}".$typeName."</a>  <a onclick=\"AlertMsg('快捷编辑窗口','$id');\" href=\"javascript:;\"><img src='images/write2.gif'/></a></td>
                    <td width='70' class='bline'>ID：".$id."</td>
                    <td width='10%' class='bline'>外链页面</td>
                    <td align='right' class='bline'>
                        <a href='{$typeDir}' target='_blank'>预览</a> |
                        <a href='catalog_edit.php?id={$id}'>更改</a> |
                        <a href='catalog_do.php?dopost=moveCatalog&typeid={$id}'>移动</a> |
                        <a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>&nbsp;
                        <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25px;height:20px'>
                    </td>
                    </tr>";
                }
                echo "<tr><td id='suns".$id."' style='display:none'><table width='100%' border='0' cellspacing='0' cellpadding='0'>";
                $this->LogicListAllSunType($id,$step." ");
                echo "</table></td></tr>\r\n";
            }
        }
    }

    /**
     *  返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
     *
     * @access    public
     * @param     int   $id  栏目ID
     * @param     int   $channel  频道ID
     * @return    array
     */
    function GetSunTypes($id, $channel=0) {
        $this->dsql = $GLOBALS['dsql'];
        $this->idArray[$this->idCounter]=$id;
        $this->idCounter++;
        $fid = $id;
        if($channel!=0) {
            $csql = " And channeltype=$channel ";
        } else {
            $csql = "";
        }
        $this->dsql->SetQuery("SELECT id FROM `#@__arctype` WHERE reid=$id $csql");
        $this->dsql->Execute("gs".$fid);

        //if($this->dsql->GetTotalRow("gs".$fid)!=0)
        //{
        while($row=$this->dsql->GetObject("gs".$fid)) {
            $nid = $row->id;
            $this->GetSunTypes($nid,$channel);
        }
        //}
        return $this->idArray;
    }

    /**
     *  删除类目
     *
     * @access    public
     * @param     int   $id  栏目ID
     * @param     bool   $isDelFile  是否删除文件
     * @return    string
     */
    function DelType($id, $isDelFile) {
        $this->idCounter = 0;
        $this->idArray = "";
        $this->GetSunTypes($id);
        $query = "
        SELECT #@__arctype.*, #@__channeltype.typename AS ctypename, #@__channeltype.addtable
        FROM `#@__arctype` LEFT JOIN #@__channeltype
        ON #@__channeltype.id=#@__arctype.channeltype
        WHERE #@__arctype.id='$id' ";
        $typeinfos = $this->dsql->GetOne($query);
        $topinfos = $this->dsql->GetOne("SELECT moresite,siteurl FROM `#@__arctype` WHERE id='".$typeinfos['topid']."'");
        if(!is_array($typeinfos)) {
            return FALSE;
        }
        $indir = $typeinfos['typedir'];
        $addtable = $typeinfos['addtable'];
        $ispart = $typeinfos['ispart'];
        $defaultname = $typeinfos['defaultname'];

        //删除数据库里的相关记录
        foreach($this->idArray as $id) {
            $myrow = $this->dsql->GetOne("SELECT * FROM `#@__arctype` WHERE id='$id'");
            if($myrow['topid']>0) {
                $mytoprow = $this->dsql->GetOne("SELECT moresite,siteurl FROM `#@__arctype` WHERE id='".$myrow['topid']."'");
                if(is_array($mytoprow) && !empty($mytoprow)) {
                    foreach($mytoprow as $k=>$v) {
                        if(!preg_match("/[0-9]/",$k)) {
                            $myrow[$k] = $v;
                        }
                    }
                }
            }

            //删除目录和目录里的所有文件 ### 禁止了此功能
            //删除单独页面
            if($myrow['ispart']==2 && $myrow['typedir']=='') {
                if( is_file($this->baseDir.'/'.$myrow['defaultname']) ) {
                    @unlink($this->baseDir.'/'.$myrow['defaultname']);
                }
            }

            //删除数据库信息
            $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__arctype` WHERE id='$id'");
            $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__arctiny` WHERE typeid='$id'");
            $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__archives` WHERE typeid='$id'");
            $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__spec` WHERE typeid='$id'");
            $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__feedback` WHERE typeid='$id'");
            if($addtable!="") {
                $this->dsql->ExecuteNoneQuery("DELETE FROM $addtable WHERE typeid='$id'");
            }
        }

        //删除目录和目录里的所有文件 ### 禁止了此功能
        //删除单独页面
        if($ispart==2 && $indir=="") {
            if( is_file($this->baseDir."/".$defaultname) ) {
                @unlink($this->baseDir."/".$defaultname);
            }
        }
        @reset($this->idArray);
        $this->idCounter = 0;
        return TRUE;
    }

    /**
     *  删除指定目录的所有文件
     *
     * @access    public
     * @param     string  $indir  指定目录
     * @return    int
     */
    function RmDirFile($indir) {
        if(!file_exists($indir)) return;
        $dh = dir($indir);
        while($file = $dh->read()) {
            if($file == "." || $file == "..") {
                continue;
            } else if(is_file("$indir/$file")) {
                @unlink("$indir/$file");
            } else {
                $this->RmDirFile("$indir/$file");
            }
            if(is_dir("$indir/$file")) {
                @rmdir("$indir/$file");
            }
        }
        $dh->close();
        return(1);
    }
}//End Class