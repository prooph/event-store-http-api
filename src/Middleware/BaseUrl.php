<?php

/**
 * This file is part of prooph/event-store-http-api.
 * (c) 2016-2019 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2016-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\Http\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Helper\UrlHelper;

final class BaseUrl implements MiddlewareInterface
{
    public const BASE_URL = '_base_url';

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(string $baseUrl, UrlHelper $urlHelper)
    {
        $this->baseUrl = $baseUrl;
        $this->urlHelper = $urlHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $uriPath = $uri->getPath();

        $request = $request->withAttribute(self::BASE_URL, $this->baseUrl);

        if ($this->baseUrl !== '/' && \strpos($uriPath, $this->baseUrl) === 0) {
            $path = \substr($uriPath, \strlen($this->baseUrl));
            $path = '/' . \ltrim($path, '/');

            $request = $request->withUri($uri->withPath($path));

            $this->urlHelper->setBasePath($this->baseUrl);
        }

        return $handler->handle($request);
    }
}
