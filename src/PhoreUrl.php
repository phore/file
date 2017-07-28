<?php


namespace Phore\File;

/**
 * Class PhoreUrl
 * @package Phore\File
 *
 * @property $GET self
 * @property $POST self
 * @property $DELETE self
 * @property $HEADER self
 * @property $PUT self
 */
class PhoreUrl
{

    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function __get($name)
    {
        return $this;
    }


    public function GET() {

    }

    public function POST() {

    }

}