<?php   if(!defined('MIINC')) exit('Request Error!');
$GLOBALS['qrcode_id'] = isset($GLOBALS['qrcode_id'])? $GLOBALS['qrcode_id'] : 1;
function lib_qrcode(&$ctag,&$refObj)
{
    global $dsql, $envs;
    //属性处理
    $attlist="type|,id|";
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);

    //var_dump($refObj->Fields['id']);

    if ( empty($type) AND empty($id) )
    {
        if ( get_class ($refObj) == 'Archives' )
        {
            $type = 'arc';
            $id = $refObj->Fields['id'];
        } elseif ( get_class($refObj)=='ListView' OR get_class($refObj)=='SgListView')
        {
            $type = 'list';
            $id = $refObj->Fields['id'];
        } elseif ( get_class($refObj) =='PartView' AND !empty($refObj->Fields['id']) )
        {
            $type = 'list';
            $id = $refObj->Fields['id'];
        } elseif ( get_class($refObj) =='PartView' AND empty($refObj->Fields['id']) )
        {
            $type = 'index';
            $id = 0;
        }
    }
    
    $reval=<<<EOT
  <a href='http://2v.dedecms.com/' id='__miqrcode_{$GLOBALS['qrcode_id']}'>玫莎二维码生成器</a>
  <script type="text/javascript">
  	var __miqrcode_id={$GLOBALS['qrcode_id']};
  	var __miqrcode_aid={$id};
  	var __miqrcode_type='{$type}';
  	var __miqrcode_dir='{$GLOBALS['cfg_plus_dir']}';
  </script>
  <script type="text/javascript" src="{$GLOBALS['cfg_plus_dir']}/img/qrcode.js"></script>
EOT;
    $GLOBALS['qrcode_id']++;
    return $reval;
}
