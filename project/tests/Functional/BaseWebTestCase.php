<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseWebTestCase extends WebTestCase
{
    const SERVER_INFO = [
      'ACCEPT' => 'application/json',
      'CONTENT_TYPE' => 'application/json'
    ];
    const DEFAULT_USER = [
        'username' => 'notgabs@gmail.com',
        'password' => 'password'
    ];
    const PAYLOAD_LOGIN = '{"username": "%s", "password": "%s"}';

    public function getResponseFromRequest(
        string $method,
        string $uri,
        string $payload = '',
        array $parameters = [],
        bool $withAuthentication = true
    ): Response {
        $client = $this->createAuthClient($withAuthentication);

        $client->request(
            $method,
            $uri . '.json',
            $parameters,
            [],
            self::SERVER_INFO,
            $payload
        );

        return $client->getResponse();
    }

    /**
     * This Authenticate or not a user and return a client containing or not a token
     */
    private function createAuthClient(bool $withAuthentication): KernelBrowser
    {
        $client = self::createClient();

        if (!$withAuthentication) {
            return $client;
        }

        $client->request(
            Request::METHOD_POST,
            '/api/login_check',
            [],
            [],
            self::SERVER_INFO,
            sprintf(self::PAYLOAD_LOGIN, self::DEFAULT_USER['username'], self::DEFAULT_USER['password'])
        );

        $data = json_decode($client->getResponse()->getContent(), true);


        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
