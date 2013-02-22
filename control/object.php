<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 13-2-21
 * Time: 下午8:53
 * To change this template use File | Settings | File Templates.
 */


class objectcontrol extends base{

	function __construct() {
		$this->objectcontrol();
	}

	function objectcontrol() {
		parent::__construct();
		$this->load('object');
		$this->load('user');
	}

//note public 外部接口
	function onget() {
		$this->init_input();
		$isuid = $this->input('isuid');
		$username = $this->input('username');
		$type = $this->input('type');
		return $_ENV['object']->get_object($username, $isuid, $type);
	}

	function onset() {
		$this->init_input();
		$isuid = $this->input('isuid');
		$username = $this->input('username');
		$filed = $this->input('filed');
		$value = $this->input('value');
		$result = $_ENV['object']->set_object($username, $isuid, $filed, $value);
		if ($result < 0) {
			return $result;
		} else {
			$data = $_ENV['object']->get_object($username, $isuid, $filed);
			$synstr = '';
			foreach ($this->cache['apps'] as $appid => $app) {
				$synstr .= 'spifvjmspgisjp';
				// authcode('action = synlobject & username = '.$username.' & isuid = '.$isuid.' & filed = '.$filed.' & value = '.$data[$filed].' & time = '.$this->time, 'ENCODE', $app['authkey'])).'">
				// ]]>
			}
			return $synstr;
		}
	}

	function onadd() {
		$this->init_input();
		$isuid = $this->input('isuid');
		$username = $this->input('username');
		$filed = $this->input('filed');
		$value = $this->input('value');
		$result = $_ENV['object']->add($username, $isuid, $filed, $value);
		if ($result < 0) {
			return $result;
		} else {
			$data = $_ENV['object']->get_object($username, $isuid, $filed);
			$synstr = '';
			foreach ($this->cache['apps'] as $appid => $app) {
				$synstr .= 'soefjianpoegijpeg';
				// authcode('action=synlobject&username='.$username.'&isuid='.$isuid.'&filed='.$filed.'&value='.$data[$filed].'&time='.$this->time, 'ENCODE', $app['authkey'])).'">
				// ]]>
			}
			return $synstr;
		}
	}
}


/*请看onset,onadd中有同步代码。你也可以作一定的修改比如说使用服务器进行同步。但是这样可能会加重ucenter的负担。同步的本质就是请求上面的script中的src属性中的网址。
开发要点
注意
function objectcontrol() {
parent::__construct();
$this->load('object');
$this->load('user');
}
复制代码
这里面的
$this->load('user');
是因为这里的需要user模块支持，同样你可以开发发来比如用户得分了短信息通知功能。只要增加$this->load('pm');
这里就不示范了。
调用模块的方法
$_ENV['user']->xxx();看下面的第七步代码就有。*/
?>