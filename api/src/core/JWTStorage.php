<?php

namespace Core;

use Firebase\JWT\JWT;

use Zend\Authentication\Storage\StorageInterface;

class JWTStorage implements StorageInterface
{
    private $jwt;

    private $jwtData;

    private $xsfr;

    private $config;

    private $request;

    public function __construct($config, $request)
    {
        $this->config = $config;
        $this->request = $request;
        $this->jwt = null;
        $this->xsfr = 0;
    }

    public function isEmpty()
    {
        return $this->jwt === null;
    }

    public function read()
    {
        $this->clear();
        $authHeader = $this->request->getHeader('authorization');
        if ($authHeader) {
            list($this->jwt) = sscanf($authHeader[0], 'Bearer %s');
            if ($this->jwt && $this->jwt != "undefined") {
                $this->jwtData = JWT::decode($this->jwt, $this->config['secret'], [$this->config['algorithm']]);
                $this->xsfr = $this->jwtData->xsfr;
            }
        }
        return $this->jwt;
    }

    public function write($contents)
    {
        $this->xsfr = md5(uniqid(rand(), true));

        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = time() + 3600;
        $this->jwtData = [
            'jti' => base64_encode(mcrypt_create_iv(32)), // Token ID
            'iat' => $issuedAt,
            'iss' => $this->config['server'],
            'nbf' => $notBefore,
            'exp' => $expire,
            'xsfr' => $this->xsfr,
            'data' => $contents
        ];

        $this->jwt = JWT::encode($this->jwtData, $this->config['secret'], $this->config['algorithm']);
    }

    public function getJWT()
    {
        return $this->jwt;
    }

    public function getJWTData()
    {
        return $this->jwtData;
    }

    public function getXSFR()
    {
        return $this->xsfr;
    }

    public function clear()
    {
        $this->jwt = null;
    }
}
