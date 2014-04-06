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

class ProviderAdapter {
    
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
     * __get
     *
     * @param $name
     * @var   string
     */
    public function __get($name)
    {
        if (in_array($name, array('title', 'link', 'thumbnail', 'duration', 'player', 'description')) AND isset($this->result[$name]))
        {
            return $this->result[$name];
        }
    }

}