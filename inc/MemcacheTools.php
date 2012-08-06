<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 06.08.12
 * Time: 00:33
 * To change this template use File | Settings | File Templates.
 */

class memcache_tools extends memcache
{
	public function delete($key, $expire=0)
	{
		parent::delete($key, $expire);
	}
}