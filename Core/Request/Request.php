<?php


namespace Core\Request;

class Request implements RequestInterface
{
    private $get;
    private $post;
    private $cookie;
    private $server;

    public function __construct()
    {
        $this->server = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);
        $this->cookie = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING);

        foreach ($_GET as $key => $item) {
            $this->get[htmlspecialchars($key, ENT_NOQUOTES)] = htmlspecialchars($item, ENT_NOQUOTES);
        }

        foreach ($_POST as $key => $item) {
            $this->post[htmlspecialchars($key, ENT_NOQUOTES)] = htmlspecialchars($item, ENT_NOQUOTES);
        }
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
        if ( isset($this->post[$buttonName]) ) {
            return true;
        }

        return false;
    }

    public function getContent(): string
    {
        if ( $this->server['REQUEST_METHOD'] === 'PUT' ) {
            parse_str(file_get_contents("php://input"),$put_vars);

            return $put_vars['body'];
        }

        return $this->post['body'];
    }
}