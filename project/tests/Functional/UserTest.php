<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends BaseWebTestCase
{
    public function testGetUsers(): void
    {
        $response = $this->getResponseFromRequest(
            Request::METHOD_GET,
            '/api/users',
            '',
            [],
            false
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertIsArray(json_decode($response->getContent(), true));
    }
}
