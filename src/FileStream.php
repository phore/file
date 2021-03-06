<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 28.07.17
 * Time: 12:20
 */

namespace Phore\File;


use Phore\File\Exception\FileAccessException;

class FileStream
{

    private $res;
    /**
     * @var File
     */
    private $file;

    public function __construct($res, File $file)
    {
        $this->res = $res;
        $this->file = $file;
    }


    public function flock(int $operation) : self {
        if ( ! @flock($this->res, $operation)) {
            throw new FileAccessException("Cannot flock('$this->file'): " . error_get_last()["message"]);
        }
        return $this;
    }

    public function feof() : bool {
        return @feof($this->res);
    }

    public function fwrite ($data) : self {
        if (false === @fwrite($this->res, $data))
            throw new FileAccessException("Cannot get fwrite('$this->file'): " . error_get_last()["message"]);
        return $this;
    }

    public function fread (int $length) : string {
        if (false === ($data = @fread($this->res, $length)))
            throw new FileAccessException("Cannot get fread('$this->file'): " . error_get_last()["message"]);
        return $data;
    }

    public function fgets (int $length=null) {
        if (false === ($data = @fgets($this->res, $length)) && ! @feof($this->res))
            throw new FileAccessException("Cannot get fgets('$this->file'): " . error_get_last()["message"]);
        return $data;
    }

    public function freadcsv (int $length=0, string $delimiter=",", string $enclosure='"', string $escape_char = "\\") : array {
        if (false === ($data = @fgetcsv($this->res, $length, $delimiter, $enclosure, $escape_char)) && ! @feof($this->res))
            throw new FileAccessException("Cannot get fgetcsv('$this->file'): " . error_get_last()["message"]);
        return $data;
    }

    public function fputcsv (array $fields, string $delimiter=",", string $enclosure='"', string $escape_char = "\\") : self {
        if (false === @fputcsv($this->res, $fields, $delimiter, $enclosure, $escape_char))
            throw new FileAccessException("Cannot get fgets('$this->file'): " . error_get_last()["message"]);
        return $this;
    }

    public function fclose() : File {
        if (false === @fclose($this->res))
            throw new FileAccessException("Cannot get fgets('$this->file'): " . error_get_last()["message"]);
        return $this->file;
    }

}