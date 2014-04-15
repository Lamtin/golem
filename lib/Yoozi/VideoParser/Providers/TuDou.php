<?php 

/*
 * This file is part of the Yoozi Golem package.
 *
 * (c) Yoozi Inc. <hello@yoozi.cn>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yoozi\VideoParser\Providers;

use Yoozi\VideoParser\ProviderAdapter;
use Yoozi\VideoParser\ProviderInterface;

/**
 * tudou video provider.
 *
 * @author Lamtin LI <lamtin.li@yoozi.cn>
 */
class TuDou extends ProviderAdapter implements ProviderInterface {

    /**
     * 当前视频平台的域名
     *
     * @var string
     */
    public static $domain = '.tudou.com';

    /**
     * 执行查询
     *
     * @param  string $url
     * @return mixd
     */
    public function fetch($url)
    {
        if ( ! isset($this->config['tudou']['app_key'])) 
        {
            throw new \Yoozi\VideoParser\Exceptions\ConfigException('The provider must set app_key to configs.');
            return FALSE;
        }

        $query = @file_get_contents('http://api.tudou.com/v6/tool/repaste?' . http_build_query(array(
                'app_key' => $this->config['tudou']['app_key'],
                'url'     => $url
            )));

        if ($query)
        {
            $query = json_decode($query);

            if (NULL === $query OR isset($query->error_code))
            {
                throw new \Yoozi\VideoParser\Exceptions\ApiException('Failure: ' . (isset($query->error_info) ? $query->error_info : 'The url parse failure') . '.');
                return FALSE;
            }

            return $this->fill($query);
        }

        throw new \Yoozi\VideoParser\Exceptions\HttpException('The provider stopped working.');
        return FALSE;
    }

    /**
     * 根据查询返回的填充数据
     *
     * @param  string $url
     * @return self
     */
    public function fill($data)
    {
        $this->result = array(
            'title'       => $data->itemInfo->title,
            'link'        => $data->itemInfo->playUrl,
            'thumbnail'   => $data->itemInfo->picUrl,
            'duration'    => $data->itemInfo->totalTime/1000,
            'player'      => $data->itemInfo->outerPlayerUrl,
            'description' => $data->itemInfo->description,
        );

        return $this;
    }

}