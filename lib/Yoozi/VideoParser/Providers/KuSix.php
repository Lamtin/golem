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
use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

class KuSix extends ProviderAdapter implements ProviderInterface, ArrayableInterface, JsonableInterface {

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
     * @return void
     */
    public function fetch($url)
    {
        $this->url = $url;

        preg_match('/show\/(\w+)[\.]/i', $url, $match);

        if (empty($match)) return FALSE;

        $this->vid = $match[1];

        $query     = @file_get_contents('http://v.ku6.com/fetch.htm?t=getVideo4Player&vid=' . $this->vid);

        if ($query)
        {
            $data = json_decode($query);

            if ($data->status == '1') return $this->fill($data->data);
        }

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
            'title' => $data->t,
            'link' => $this->url,
            'thumbnail' => $data->picpath,
            'duration' => $data->vtime,
            'player' => 'http://player.ku6.com/refer/' . $this->vid . '../v.swf',
            'description' => $data->desc,
        );

        return $this;
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

}