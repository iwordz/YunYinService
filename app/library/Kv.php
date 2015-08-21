<?php
/**
 * 键值对存储
 * Function list:
 * - set()
 * - get()
 * - del()
 * - flush()
 */
class Kv
{

	private static $_handler = null; //处理方式

	/**
	 * 设置缓存
	 * @method set
	 * @param  [type]  $name   [description]
	 * @param  [type]  $value  [description]
	 * @param  mixed $expire [有效时间]
	 * @author NewFuture
	 */
	public static function set($name, $value)
	{
		return self::Handler()->set($name, $value);
	}

	/**
	 * 读取缓存数据
	 * @method get
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 * @author NewFuture
	 */
	public static function get($name)
	{
		return self::Handler()->get($name);
	}

	/**
	 * 删除缓存数据
	 * @method del
	 * @param  [type] $name [description]
	 * @return [bool]
	 * @author NewFuture
	 */
	public static function del($name)
	{
		return self::Handler()->delete($name);
	}

	/**
	 * 清空存储
	 * @method fush
	 * @author NewFuture
	 */
	public static function flush()
	{
		if (Config::get('kv.type') == 'sae')
		{
			/*sae kvdb 逐个删除*/
			$kv = self::Handler();
			while ($ret = $kv->pkrget('', 100))
			{
				foreach ($ret as $k => $v)
				{
					$kv->delete(key($k));
				}
			}
		}
		else
		{
			return self::Handler()->flush();
		}

	}

	/**
	 * 获取处理方式
	 * @param  [type]  $name [description]
	 * @return $_handler
	 * @author NewFuture
	 */
	protected static function Handler()
	{
		if (null === self::$_handler)
		{
			$config = Config::get('kv');
			switch ($config['type'])
			{
				case 'sae':	//sae_memcache
					self::$_handler = memcache_init();
					break;

				case 'file':	//文件缓存
					self::$_handler = new Storage\File($config['dir'], false);
					break;

				default:
					throw new Exception('未定义方式' . $config['type']);
			}
		}
		return self::$_handler;
	}
}
