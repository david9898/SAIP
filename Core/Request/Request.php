<?php


namespace Core\Request;

class Request implements RequestInterface
{
    private $get;
    private $post;

    public function __construct()
    {
        $this->get  = $_GET;
        $this->post = $_POST;
    }

    public function getPOST(): array
    {
        return $this->post;
    }

    public function getGET(): array
    {
        return $this->get;
    }

    public function isSubmit(string $buttonName): bool
    {
        if ( isset($this->post[$buttonName]) || isset($this->get[$buttonName]) ) {
            return true;
        }

        return false;
    }

    public function getContent(): string
    {
        $arr = array_keys($this->post);

        return $arr[0];
    }
}