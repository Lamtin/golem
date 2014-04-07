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

/**
 * Video parser.
 *
 * @author Lamtin LI <lamtin.li@yoozi.cn>
 */
class VideoParser {

    /**
     * 当前实例化的对象
     *
     * @var self
     */
    protected static $instance;

    /**
     * 支持的视频平台
     *
     * @var array
     */
    protected $providers = array();

    /**
     * 配置参数
     *
     * @var array
     */
    protected $config    = array();

    /**
     * gets the instance via lazy initialization (created on first usage)
     *
     * @return self
     */
    public static function getInstance()
    {
        
        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * 构造方法
     *
     * @return void
     */
    public function __construct()
    {
        $this->scanDefaultProviders();
    }

    /**
     * 载入配置
     *
     * @param  array $config
     * @return self
     */
    public function setConfig($config = array())
    {
        $this->config = $config;
        return $this;
    }

    /**
     * 根据视频播放页查询视频信息
     *
     * @param  string $url
     * @return Yoozi\VideoParser\ProviderInterface
     */
    public function get($url)
    {
        foreach ($this->providers as $provider)
        {
            if (preg_match('/(' . $provider::$domain . ')/i', $url))
            {
                $provider = new $provider($this->config ?: array());
                return $provider->fetch($url);
            }
        }

        throw new \Yoozi\VideoParser\Exceptions\NotSupportException('The url was not supported.');

        return FALSE;
    }

    /**
     * 增加视频平台解析类的命名空间
     *
     * @param  string $provider
     * @return void
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * 添加默认支持的视频平台解析类的命名空间
     *
     * @return void
     */
    public function scanDefaultProviders()
    {
        $scanResult = scandir(__DIR__ . '/Providers');

        foreach ($scanResult as $scanRow) 
        {
            if (strpos($scanRow, '.php') > 0)
            {
                $this->addProvider('\Yoozi\VideoParser\Providers\\' . substr($scanRow, 0, -4));
            }
        }
    }

}