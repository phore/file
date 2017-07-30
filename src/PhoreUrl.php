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

    /**
     * @var array
     */
    private $url;

    private $method;

    /**
     * @var PhoreStreamReceiver[]
     */
    private $receiver = [];

    private $onHeader = null;

    public function __construct(string $url)
    {
        $this->url = $url;
    }


    public function xpath($subpath) : self {
        $subpath = new PhorePath($subpath, PhorePath::TYPE_URL);
        $path = $this->url;
        if (substr($path,-1) == "/")
            $path .= $subpath->toRelative();
        else
            $path .= $subpath->toAbsolute();
        $this->url = $path;
        return new self($path);
    }


    public function in($data=null) : self {


    }

    public function inYaml($data) : self {

    }

    public function inJson($data) : self {

    }


    public function into(&$ref, PhoreUrlHeader &$header = null) : self {
        $header = new PhoreUrlHeader();
        $ref = "";
        $this->addHandler(new class ($ref) implements PhoreStreamReceiver {

            private $ref;

            public function __construct(&$ref)
            {
                $this->ref =& $ref;
            }

            public function onResult(PhoreUrlResult $header)
            {
                // TODO: Implement onResult() method.
            }

            public function onData($data)
            {
                $this->ref .= $data;
            }
        });
        return $this;
    }

    public function outYaml(&$ref) : self {

    }

    public function outJson(&$ref) : self {

    }

    public function outFile($filename) : self {

    }

    public function outHeader(&$header) : self {
        $this->onHeader = function ($data) use (&$header) {
            echo "header";
            $header = $data;
        };
        return $this;
    }

    public function addHandler (PhoreStreamReceiver $receiver) : self {
        $this->receiver[] = $receiver;
        return $this;
    }


    public function run($timeLimit=30) : self {
        $ch = curl_init($this->url);
        if ($this->method === null)
            $this->method = "GET";

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeLimit);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $onBody = false;
        $buf = "";
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $str) use (&$buf, &$onBody) {
            if ($onBody === false) {
                echo ".";
                $buf .= $str;
                if (($pos = strpos($buf, "\r\n\r\n") ) !== false) {
                    //echo "END OF HEADER: $str";

                    $onBody = true;
                    $headerStr = substr($buf, 0, $pos);
                    if ($this->onHeader !== null)
                        ($this->onHeader)($headerStr);


                    if (strpos(strtoupper($buf), "\nLOCATION:")) {
                        $onBody = false;
                        $buf = "";
                        return strlen($str);
                    }
                    $body = substr($buf, $pos+4);
                    $buf = "";
                    if ($body != "") {
                        foreach ($this->receiver as $curRec) {
                            $curRec->onData($body);
                        }
                    }
                }
            } else {
                foreach ($this->receiver as $curRec)
                    $curRec->onData($str);
            }
            return strlen($str);
        });
        if ( ! curl_exec($ch)) {
            throw new \Exception("Error loading '$this->url': " . curl_error($ch));
        }
        return $this;
    }


    public function new () {

    }

    public function wait($timeLimit=30, $parallel=5) {

    }

}