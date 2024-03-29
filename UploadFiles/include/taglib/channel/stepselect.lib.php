<?php
if(!defined('MIINC')) exit("Request Error!");

function ch_stepselect($fvalue,&$arcTag,&$refObj,$fname='')
{
    return GetEnumsValue2($fname,$fvalue);
}

/**
 * 获取二级枚举的值
 *
 * @version        $Id: stepselect.lib.php
 * @package        Mi.Taglib
 * @copyright      Copyright (c)  2010, Missra
 * @license        http://help.missra.com/usersguide/license.html
 * @link           http://www.missra.com
 */
function GetEnumsValue2($egroup,$evalue=0)
{
    if( !isset($GLOBALS['em_'.$egroup.'s']) )
    {
        $cachefile = MIDATA.'/enums/'.$egroup.'.php';
        if(!file_exists($cachefile))
        {
            require_once(MIINC.'/enums.func.php');
            WriteEnumsCache();
        }
        if(!file_exists($cachefile))
        {
            return '';
        }
        else
        {
            require_once($cachefile);
        }
    }
    if($evalue>=500)
    {
        if($evalue % 500 == 0)
        {
            return (isset($GLOBALS['em_'.$egroup.'s'][$evalue]) ? $GLOBALS['em_'.$egroup.'s'][$evalue] : '');
        }
        else if (preg_match("#([0-9]{1,})\.([0-9]{1,})#", $evalue, $matchs))
        {
            $esonvalue = $matchs[1];
            $etopvalue = $esonvalue - ($esonvalue % 500);
            $esecvalue = $evalue;
            $GLOBALS['em_'.$egroup.'s'][$etopvalue] = empty($GLOBALS['em_'.$egroup.'s'][$etopvalue])? '' 
                                                     : $GLOBALS['em_'.$egroup.'s'][$etopvalue];
            $GLOBALS['em_'.$egroup.'s'][$esonvalue] = empty($GLOBALS['em_'.$egroup.'s'][$esonvalue])? '' 
                                                     : $GLOBALS['em_'.$egroup.'s'][$esonvalue];
            $GLOBALS['em_'.$egroup.'s'][$esecvalue] = empty($GLOBALS['em_'.$egroup.'s'][$esecvalue])? '' 
                                                     : $GLOBALS['em_'.$egroup.'s'][$esecvalue];
            return $GLOBALS['em_'.$egroup.'s'][$etopvalue].' -- '.$GLOBALS['em_'.$egroup.'s'][$esonvalue].' -- '.$GLOBALS['em_'.$egroup.'s'][$esecvalue];
        }
        else
        {
            $elimit = $evalue % 500;
            $erevalue = $evalue - $elimit;
            $GLOBALS['em_'.$egroup.'s'][$erevalue] = empty($GLOBALS['em_'.$egroup.'s'][$erevalue])? '' 
                                                     : $GLOBALS['em_'.$egroup.'s'][$erevalue];
            $GLOBALS['em_'.$egroup.'s'][$evalue] = empty($GLOBALS['em_'.$egroup.'s'][$evalue])? '' 
                                                     : $GLOBALS['em_'.$egroup.'s'][$evalue];
            return $GLOBALS['em_'.$egroup.'s'][$erevalue].' -- '.$GLOBALS['em_'.$egroup.'s'][$evalue];
        }
    }
}