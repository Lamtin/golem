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
 * youku video provider.
 *
 * @author Lamtin LI <lamtin.li@yoozi.cn>
 */
class YouKu extends ProviderAdapter implements ProviderInterface {

    /**
     * 当前视频平台的域名
     *
     * @var string
     */
    public static $domain = '.youku.com';

    /**
     * 执行查询
     *
     * @param  string $url
     * @return mixd
     */
    public function fetch($url)
    {
        if ( ! isset($this->config['youku']['client_id'])) 
        {
            throw new \Yoozi\VideoParser\Exceptions\ConfigException('The provider must set client_id to configs.');
            return FALSE;
        }

        $query = @file_get_contents('https://openapi.youku.com/v2/videos/show_basic.json?' . http_build_query(array(
                'client_id' => $this->config['youku']['client_id'],
                'video_url' => $url
            )));

        if ($query)
        {
            return $this->fill(json_decode($query));
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
            'title'       => $data->title,
            'link'        => $data->link,
            'thumbnail'   => $data->thumbnail,
            'duration'    => $data->duration,
            'player'      => $data->player,
            'description' => $data->description,
        );

        return $this;
    }

}