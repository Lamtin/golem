<?php

/*
 * This file is part of the Yoozi Golem package.
 *
 * (c) Yoozi Inc. <hello@yoozi.cn>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yoozi\VideoParser;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

/**
 * Provider adapter.
 *
 * @author Lamtin LI <lamtin.li@yoozi.cn>
 */
class ProviderAdapter implements ArrayableInterface, JsonableInterface {
    
    /**
     * 当前视频平台的域名
     *
     * @var string
     */
    public static $domain = '';

    /**
     * 查询返回的数据
     *
     * @var array
     */
    protected $result;

    /**
     * 执行查询的页面URL
     *
     * @var string
     */
    protected $url = '';

    /**
     * 配置参数
     *
     * @var array
     */
    protected $config = array();

    /**
     * 构造方法
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = array())
    {
        $this->config = $config;
    }

    /**
     * 返回结果数组
     *
     * @return array
     */
    public function toArray()
    {
        return $this->result;
    }

    /**
     * 返回结果JSON字符串
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->result, $options);
    }

    /**
     * __get
     *
     * @param  $name
     * @return string
     */
    public function __get($name)
    {
        if (in_array($name, array('title', 'link', 'thumbnail', 'duration', 'player', 'description')) AND isset($this->result[$name]))
        {
            return $this->result[$name];
        }
    }

}