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

interface ProviderInterface {

    /**
     * 执行查询
     *
     * @param  string $url
     * @return void
     */
    public function fetch($url);

    /**
     * 根据查询返回的填充数据
     *
     * @param  string $url
     * @return self
     */
    public function fill($data);

}