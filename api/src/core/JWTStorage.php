<?php

namespace Core;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;

use Zend\Authentication\Storage\StorageInterface;

class JWTStorage implements StorageInterface
{
    private $token;

    private $signer;

    private $secret;

    private $source;

    public function __construct($secret, $source = "")
    {
        $this->secret = $secret;
        $this->source = $source;
        $this->signer = new Sha256();
        $this->token = null;
    }

    public function isEmpty()
    {
        return $this->token === null;
    }

    public function read()
    {
        $this->clear();
        if (strlen($this->source) > 0) {
            $this->token = (new Parser())->parse($this->source);
            if ($this->token->verify($this->signer, $this->secret)) {
                return $this->token;
            }
        }
        return null;
    }

    public function write($contents)
    {
        // $contents contains a user model
        $this->token = (new Builder())
            ->setIssuer('clubman')
            ->setId(base64_encode(mcrypt_create_iv(32)), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration(time() + 3600)
            ->set('xsfr', md5(uniqid(rand(), true)))
            ->set('data', [
                'id' => $contents->id
            ])
            ->sign($this->signer, $this->secret)
            ->getToken();
        return $this->token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function clear()
    {
        $this->jwt = null;
    }
}
