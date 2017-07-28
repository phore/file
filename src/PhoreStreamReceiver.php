<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 28.07.17
 * Time: 15:12
 */

namespace Phore\File;


interface PhoreStreamReceiver
{
    public function onResult(PhoreUrlResult $header);
    public function onData($data);
}