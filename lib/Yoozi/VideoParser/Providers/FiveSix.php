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
 * 56 video provider.
 *
 * @author Lamtin LI <lamtin.li@yoozi.cn>
 */
class FiveSix extends ProviderAdapter implements ProviderInterface {

    /**
     * 当前视频平台的域名
     *
     * @var string
     */
    public static $domain = '.56.com';

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
        if ( ! isset($this->config['56']['app_key']) OR  ! isset($this->config['56']['secret'])) 
        {
            throw new \Yoozi\VideoParser\Exceptions\ConfigException('The provider must set app_key and secret to configs.');
            return FALSE;
        }

        preg_match('/\/v_(\w+)[\.]/i', $url, $match);

        if (empty($match)) 
        {
            throw new \Yoozi\VideoParser\Exceptions\ApiException('The url parse failure.');
            
            return FALSE;
        }

        $this->vid = $match[1];

        $query = @file_get_contents('http://oapi.56.com/video/getVideoInfo.json?' . $this->genParamAndSign(array(
                'vid' => $this->vid
            )));

        if ($query)
        {
            $query = array_values((array) json_decode($query));
            $query = $query[0];

            if (NULL === $query OR isset($query->errno) OR ! isset($query->title))
            {
                throw new \Yoozi\VideoParser\Exceptions\ApiException('Failure: ' . (isset($query->errno) ? $query->err : 'The url parse failure') . '.');
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
            'title'       => $data->title,
            'link'        => $data->url,
            'thumbnail'   => $data->img,
            'duration'    => $data->totaltime/1000,
            'player'      => $data->swf,
            'description' => $data->desc,
        );

        return $this;
    }

    /**
     * @description 转码异常字符
     * 
     * @param  mixed $input
     * @return void
     */
    private function urlencodeRfc3986($input)
    { 
        if (is_array($input))
        {
            return array_map( array('self', 'urlencodeRfc3986') , $input );
        }
        else if(is_scalar($input))
        {
            return str_replace( '+' , ' ' , str_replace( '%7E' , '~' , rawurlencode($input)));
        }
        else
        {
            return '';
        }
    }

    /**
     * @description 签名方法实现，并构造一个参数串
     * 
     * @param  array $params
     * @return void
     */
    private function genParamAndSign($params)
    {
        $keys   = $this->urlencodeRfc3986(array_keys($params));
        $values = $this->urlencodeRfc3986(array_values($params));

        $params = array_combine($keys, $values);

        ksort($params);
 
        $req =  md5(http_build_query($params));
        $ts  =  time();

        $params['sign']   = md5($req . '#' . $this->config['56']['app_key'] . '#' . $this->config['56']['secret'] . '#' . $ts);
        $params['appkey'] = $this->config['56']['app_key'];
        $params['ts']     = $ts;

        return http_build_query($params);
    }

}