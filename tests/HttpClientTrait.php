<?php
declare(strict_types=1);


namespace Tests;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait HttpClientTrait
{
    protected ?HttpClientInterface $client = null;
    protected ?ResponseInterface $response = null;

    public function hasClient(): bool
    {
        return $this->client !== null;
    }

    public function withHttpClient(string $baseUrl): self
    {
        $this->client = HttpClient::createForBaseUri($baseUrl);
        return $this;
    }

    public function login(string $username, string $password): self
    {
        static $accessToken = null;
        if ($accessToken == null) {
            try {
                $response = $this->client->request(
                    'POST',
                    '/auth/login',
                    [
                        'body' => [
                            'username' => $username,
                            'password' => $password
                        ]
                    ]
                );
                if ($response->getStatusCode() === 500) {
                    var_dump($response->getContent(false));
                }
            } catch (TransportExceptionInterface $e) {
                $this->fail((string) $e);
            }
            try {
                $result = $response->toArray();
            } catch (ExceptionInterface $e) {
                $this->fail((string) $e);
            }
            $accessToken = $result['access_token'];
        }

        $this->client = $this->client->withOptions([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);
        return $this;
    }

    public function get(string $path, array $query = []): ResponseInterface
    {
        try {
            $response = $this->client->request(
                'GET',
                $path,
                [
                    'query' => $query
                ]
            );
            expect($response)
                ->when(
                    $response->getStatusCode() === 500,
                    fn ($response) => $response->getContent(false)->dd()
                )
            ;
        } catch (TransportExceptionInterface $e) {
            $this->fail((string) $e);
        }
        return $response;
    }

    public function post(string $path, array $data): ResponseInterface
    {
        try {
            $response = $this->client->request(
                'POST',
                $path,
                [
                    'json' => $data
                ]
            );
            expect($response)
                ->when(
                    $response->getStatusCode() === 500,
                    fn ($response) => $response->getContent(false)->dd()
                )
            ;
        } catch (TransportExceptionInterface $e) {
            $this->fail((string) $e);
        }
        return $response;
    }

    public function patch(string $path, array $data): ResponseInterface
    {
        try {
            $response = $this->client->request(
                'PATCH',
                $path,
                [
                    'json' => $data
                ]
            );
            expect($response)
                ->when(
                    $response->getStatusCode() === 500,
                    fn ($response) => $response->getContent(false)->dd()
                )
            ;
        } catch (TransportExceptionInterface $e) {
            $this->fail((string) $e);
        }
        return $response;
    }
}
