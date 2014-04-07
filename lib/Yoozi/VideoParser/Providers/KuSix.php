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
 * ku6 video provider.
 *
 * @author Lamtin LI <lamtin.li@yoozi.cn>
 */
class KuSix extends ProviderAdapter implements ProviderInterface {

    /**
     * 当前视频平台的域名
     *
     * @var string
     */
    public static $domain = '.ku6.com';

    /**
     * 当前视频的VID
     *
     * @var string
     */
    protected $vid = '';

    /**
     * 执行查询
     *
     * @param  string $url
     * @return mixd
     */
    public function fetch($url)
    {
        $this->url = $url;

        preg_match('/show\/(\w+)[\.]/i', $url, $match);

        if (empty($match)) 
        {
            throw new \Yoozi\VideoParser\Exceptions\ApiException('The url parse failure.');
            
            return FALSE;
        }

        $this->vid = $match[1];

        $query     = @file_get_contents('http://v.ku6.com/fetch.htm?t=getVideo4Player&vid=' . $this->vid);

        if ($query)
        {
            $data = json_decode($query);

            if ($data->status == '1') return $this->fill($data->data);

            throw new \Yoozi\VideoParser\Exceptions\ApiException('The url parse failure.');

            return FALSE;
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
            'title'       => $data->t,
            'link'        => $this->url,
            'thumbnail'   => $data->picpath,
            'duration'    => $data->vtime,
            'player'      => 'http://player.ku6.com/refer/' . $this->vid . '../v.swf',
            'description' => $data->desc,
        );

        return $this;
    }

}