function AddNew() {
    $MI('addTab').style.display = 'block';
}

function CloseTab(tb) {
    $MI(tb).style.display = 'none';
}

function ListAll() {
    $MI('editTab').style.display = 'block';
    var myajax = new MiAjax($MI('editTabBody'));
    myajax.SendGet('index_body.php?dopost=editshow');
}

function LoadUpdateInfos() {
    $MI('updateinfos').innerHTML = "<div style=\"height:90px;\"><img src='images/loadinglit.gif' /> 正在处理中...</div>";
    var myajax = new MiAjax($MI('updateinfos'));
    myajax.SendGet('update_guide.php?dopost=test');
}

function SkipReload(nnum) {
    if (window.confirm("忽略后以后都不会再提示这个日期前的升级信息，你确定要忽略这些更新吗?")) {
        MiXHTTP = null;
        $MI('updateinfos').innerHTML = "<img src='images/loadinglit.gif' /> 正在处理中...";
        var myajax = new MiAjax($MI('updateinfos'));
        myajax.SendGet('update_guide.php?dopost=skip&vtime=' + nnum);
    }
}

function ShowWaitDiv() {
    $MI('loaddiv').style.display = 'block';
    return true;
}

window.onload = function () {
    var myajax = new MiAjax($MI('listCount'));
    myajax.SendGet('index_body.php?dopost=getRightSide');
};