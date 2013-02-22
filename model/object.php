<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 13-2-21
 * Time: 下午9:03
 * To change this template use File | Settings | File Templates.
 */
!defined('IN_UC') && exit('Access Denied');

class objectmodle {
	var $db;
	var $base;

	function __construct(&$base) {
		$this->objectmodle($base);
	}

	function objectmodel(&$base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function get_object($username, $isuid, $type = null) {
		if ($type == null) {
			$type = '*';
		} else {
			$type = 'uid,username,' . $type;
		}
		if (!$isuid) {
			$where = 'username=\'' . $username . '\'';
		} else {
			$where = 'uid=\'' . $username . '\'';
		}
		$arr = $this->db->fetch_first("SELECT " . $type . " FROM " . UC_DBTABLEPRE . "object WHERE " . $where);
		return $arr;
	}

	function set_object($username, $isuid, $filed, $value) {
		file_put_contents('sql.txt', $sqladd);
		if (!$isuid) {
			$where = 'username=\'' . $username . '\'';
		} else {
			$where = 'uid=\'' . $username . '\'';
		}
		$sqladd = '';
		if (is_array($filed)) {
			foreach ($filed as $k => $v) {
				$sqladd .= ',`' . $k . '`=\'' . $v . '\'';
			}
		} elseif ($filed && $value) {
			$sqladd = ',' . $filed . '=\'' . $value . '\'';
		}
		if ($this->get_object($username, $isuid)) {
			if (is_array($filed)) {
				foreach ($filed as $k => $v) {
					$sqladd .= ',`' . $k . '`=\'' . $v . '\'';
				}
			}
			$this->db->query("UPDATE " . UC_DBTABLEPRE . "object SET " . substr($sqladd, 1) . " WHERE " . $where);
			file_put_contents('update.txt', "UPDATE " . UC_DBTABLEPRE . "object SET " . substr($sqladd, 1) . " WHERE " . $where);
			return $this->db->affected_rows();
		} else {
			if (!$isuid) {
				$status = $_ENV['user']->get_user_by_username($username);
			} else {
				$status = $_ENV['user']->get_user_by_uid($username);
			}
			if ($status) {
				$uid = $status['uid'];
				$username = $status['username'];
			}
			$this->db->query("INSERT INTO " . UC_DBTABLEPRE . "object SET username='$username', uid=$uid" . $sqladd);
			return $this->db->affected_rows();
		}
		return $arr;
	}

	function add($username, $isuid, $filed, $value) {
		$data = $this->get_object($username, $isuid, $filed, $value);
		if (!$data) {
			$this->set_object($username, $isuid, null, null);
			$data = $this->get_object($username, $isuid, $filed, $value);
		}
		if ($data[$filed] + $value < 0) {
			return -11;
		}
		$sqladd = $filed . '=' . $filed . '+' . $value;
		if (!$isuid) {
			$where = 'username=\'' . $username . '\'';
		} else {
			$where = 'uid=\'' . $username . '\'';
		}
		$this->db->query("UPDATE " . UC_DBTABLEPRE . "object SET " . $sqladd . " WHERE " . $where);
		return $this->db->affected_rows();
	}
}

/*
objectmodel($base);
}
}*/


/*
 到此ucenter修改完成。
下面就讲讲如何进行积分同步。这里只以uchome为例。其它自行修改或找本人付费修改。
打开uchome下的api/uc.php
查找
if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcredit', 'getcreditsettings', 'updatecreditsettings', 'addfeed'))) {
复制代码
修改为
if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcredit', 'getcreditsettings', 'updatecreditsettings', 'addfeed','synlobject'))) {
复制代码
上面只是增加了,'synlobject'
在note的类中增加如下函数

function synlobject ($get,$post){
global $_SGLOBAL;
if(intval($get['isuid'])){
$uid=intval($get['username']);
}else{
$username=$get['username'];
}
//print_r($get);
$value=$get['value'];
$filed=$get['filed'];
switch($filed){
case 'credit':
if(intval($get['isuid'])){
//echo "UPDATE ".tname('space')." SET credit='$value' WHERE uid='$uid'";
$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit='$value' WHERE uid='$uid'");
}else{
//echo "UPDATE ".tname('space')." SET credit='$value' WHERE username='$username'";
$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit='$value' WHERE username='$username'");
}
default:
}
//return API_RETURN_SUCCEED;
}

完整内容如下：

3600) {
exit('Authracation has expiried');
}
if(empty($get)) {
exit('Invalid Request');
}
include_once S_ROOT.'./uc_client/lib/xml.class.php';
$post = xml_unserialize(file_get_contents('php://input'));
if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcredit', 'getcreditsettings', 'updatecreditsettings', 'addfeed','synlobject'))) {
$uc_note = new uc_note();
echo $uc_note->$get['action']($get, $post);
exit();
} else {
exit(API_RETURN_FAILED);
}
}
class uc_note {
var $dbconfig = '';
var $db = '';
var $tablepre = '';
var $appdir = '';
function _serialize($arr, $htmlon = 0) {
if(!function_exists('xml_serialize')) {
include_once S_ROOT.'./uc_client/lib/xml.class.php';
}
return xml_serialize($arr, $htmlon);
}
function uc_note() {
global $_SGLOBAL, $_SC;
$this->appdir = substr(dirname(__FILE__), 0, -3);
$this->dbconfig = S_ROOT.'./config.php';
$this->db = $_SGLOBAL['db'];
$this->tablepre = $_SC['tablepre'];
}
function test($get, $post) {
return API_RETURN_SUCCEED;
}
function deleteuser($get, $post) {
global $_SGLOBAL;
if(!API_DELETEUSER) {
return API_RETURN_FORBIDDEN;
}
//note 用户删除 API 接口
include_once S_ROOT.'./source/function_delete.php';
//获得用户
$uids = $get['ids'];
$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('member')." WHERE uid IN ($uids)");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
deletespace($value['uid'], 1);
}
return API_RETURN_SUCCEED;
}
function renameuser($get, $post) {
global $_SGLOBAL;
if(!API_RENAMEUSER) {
return API_RETURN_FORBIDDEN;
}
//编辑用户
$old_username = $get['oldusername'];
$new_username = $get['newusername'];
$_SGLOBAL['db']->query("UPDATE ".tname('member')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('thread')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('tagspace')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('session')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('post')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('poke')." SET fromusername='$new_username' WHERE fromusername='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('notification')." SET author='$new_username' WHERE author='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('friend')." SET fusername='$new_username' WHERE fusername='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('feed')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('doing')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('comment')." SET author='$new_username' WHERE author='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('blog')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('album')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('share')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('poll')." SET username='$new_username' WHERE username='$old_username'");
$_SGLOBAL['db']->query("UPDATE ".tname('event')." SET username='$new_username' WHERE username='$old_username'");
return API_RETURN_SUCCEED;
}
function gettag($get, $post) {
global $_SGLOBAL;
if(!API_GETTAG) {
return API_RETURN_FORBIDDEN;
}
$name = trim($get['id']);
if(empty($name) || !preg_match('/^([\x7f-\xff_-]|\w)+$/', $name) || strlen($name) > 20) {
return API_RETURN_FAILED;
}
$tag = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname('tag')." WHERE tagname='$name'"));
if($tag['closed']) {
return API_RETURN_FAILED;
}
$PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
$siteurl = 'http://'.$_SERVER['HTTP_HOST'].preg_replace("/\/+(api)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/';
$query = $_SGLOBAL['db']->query("SELECT b.*
FROM ".tname('tagblog')." tb, ".tname('blog')." b
WHERE b.blogid=tb.blogid AND tb.tagid='$tag[tagid]' AND b.friend=0
ORDER BY b.dateline DESC
LIMIT 0,10");
$bloglist = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
$bloglist[] = array(
'subject' => $value['subject'],
'uid' => $value['uid'],
'username' => $value['username'],
'dateline' => $value['dateline'],
'url' => $siteurl."space.php?uid=$value[uid]&do=blog&id=$value[blogid]",
'spaceurl' => $siteurl."space.php?uid=$value[uid]"
);
}
$return = array($name, $bloglist);
return $this->_serialize($return, 1);
}
function synlogin($get, $post) {
global $_SGLOBAL;
if(!API_SYNLOGIN) {
return API_RETURN_FORBIDDEN;
}
//note 同步登录 API 接口
obclean();
header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
$cookietime = 31536000;
$uid = intval($get['uid']);
$query = $_SGLOBAL['db']->query("SELECT uid, username, password FROM ".tname('member')." WHERE uid='$uid'");
if($member = $_SGLOBAL['db']->fetch_array($query)) {
include_once S_ROOT.'./source/function_space.php';
$member = saddslashes($member);
$space = insertsession($member);
//设置cookie
ssetcookie('auth', authcode("$member[password]\t$member[uid]", 'ENCODE'), $cookietime);
}
ssetcookie('loginuser', $get['username'], $cookietime);
}
function synlogout($get, $post) {
global $_SGLOBAL;
if(!API_SYNLOGOUT) {
return API_RETURN_FORBIDDEN;
}
//note 同步登出 API 接口
obclean();
header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
clearcookie();
}
function updatepw($get, $post) {
global $_SGLOBAL;
if(!API_UPDATEPW) {
return API_RETURN_FORBIDDEN;
}
$username = $get['username'];
$newpw = md5(time().rand(100000, 999999));
$_SGLOBAL['db']->query("UPDATE ".tname('member')." SET password='$newpw' WHERE username='$username'");
return API_RETURN_SUCCEED;
}
function updatebadwords($get, $post) {
global $_SGLOBAL;
if(!API_UPDATEBADWORDS) {
return API_RETURN_FORBIDDEN;
}
$data = array();
if(is_array($post)) {
foreach($post as $k => $v) {
$data['findpattern'][$k] = $v['findpattern'];
$data['replace'][$k] = $v['replacement'];
}
}
$cachefile = S_ROOT.'./uc_client/data/cache/badwords.php';
$fp = fopen($cachefile, 'w');
$s = " fwrite($fp, $s);
fclose($fp);
return API_RETURN_SUCCEED;
}
function updatehosts($get, $post) {
global $_SGLOBAL;
if(!API_UPDATEHOSTS) {
return API_RETURN_FORBIDDEN;
}
$cachefile = S_ROOT.'./uc_client/data/cache/hosts.php';
$fp = fopen($cachefile, 'w');
$s = " fwrite($fp, $s);
fclose($fp);
return API_RETURN_SUCCEED;
}
function updateapps($get, $post) {
global $_SGLOBAL;
if(!API_UPDATEAPPS) {
return API_RETURN_FORBIDDEN;
}
$UC_API = '';
if($post['UC_API']) {
$UC_API = $post['UC_API'];
unset($post['UC_API']);
}
$cachefile = S_ROOT.'./uc_client/data/cache/apps.php';
$fp = fopen($cachefile, 'w');
$s = "' ? substr($configfile, 0, -2) : $configfile;
$configfile = preg_replace("/define\('UC_API',\s*'.*?'\);/i", "define('UC_API', '$UC_API');", $configfile);
if($fp = @fopen(S_ROOT.'./config.php', 'w')) {
@fwrite($fp, trim($configfile));
@fclose($fp);
}
}
return API_RETURN_SUCCEED;
}
function updateclient($get, $post) {
global $_SGLOBAL;
if(!API_UPDATECLIENT) {
return API_RETURN_FORBIDDEN;
}
$cachefile = S_ROOT.'./uc_client/data/cache/settings.php';
$fp = fopen($cachefile, 'w');
$s = "query("UPDATE ".tname('space')." SET credit=credit+'$amount' WHERE uid='$uid'");
return API_RETURN_SUCCEED;
}
function getcredit($get, $post) {
global $_SGLOBAL;
if(!API_GETCREDIT) {
return API_RETURN_FORBIDDEN;
}
$uid = intval($get['uid']);
$credit = getcount('space', array('uid'=>$uid), 'credit');
return $credit;
}
function getcreditsettings($get, $post) {
global $_SGLOBAL;
if(!API_GETCREDITSETTINGS) {
return API_RETURN_FORBIDDEN;
}
$credits = array();
$credits[1] = array(lang('credit'), lang('credit_unit'));
return $this->_serialize($credits);
}
function updatecreditsettings($get, $post) {
global $_SGLOBAL;
if(!API_UPDATECREDITSETTINGS) {
return API_RETURN_FORBIDDEN;
}
$outextcredits = array();
foreach($get['credit'] as $appid => $credititems) {
if($appid == UC_APPID) {
foreach($credititems as $value) {
$outextcredits[$value['appiddesc'].'|'.$value['creditdesc']] = array(
'creditsrc' => $value['creditsrc'],
'title' => $value['title'],
'unit' => $value['unit'],
'ratio' => $value['ratio']
);
}
}
}
$cachefile = S_ROOT.'./uc_client/data/cache/creditsettings.php';
$fp = fopen($cachefile, 'w');
$s = "query("UPDATE ".tname('space')." SET credit='$value' WHERE uid='$uid'");
}else{
//echo "UPDATE ".tname('space')." SET credit='$value' WHERE username='$username'";
$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit='$value' WHERE username='$username'");
}
default:
}
//return API_RETURN_SUCCEED;
}
}
*/
?>