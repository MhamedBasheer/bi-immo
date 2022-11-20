<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class JwtDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        OpenApiFactoryInterface $decorated
    ) {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'phoneNumber' => [
                    'type' => 'string',
                    'example' => '770000000',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'apassword',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
                    'JWT Token',  // Ref
                    null,                // Summary
                    null,                // Description
                    null,                // Operation GET
                    null,                // Operation PUT
                    new Model\Operation( // Operation POST
                        'postCredentialsItem', // OperationId
                        ['Token'],    // Tags
                        [                      // Responses
                            '200' => [
                                'description' => 'Get JWT token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'Return JWT token to login', // Summary
                        '',                        // Description
                        null,                      // External Docs
                        [],                        // Parameters
                        new Model\RequestBody(     // RequestBody
                            'Generate new JWT Token',           // Description
                            new \ArrayObject([                   // Content
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ]),
                            false                               // Required
                        ),
                    ),
                );
        $openApi->getPaths()->addPath('/api/login', $pathItem);

        return $openApi;
    }
}