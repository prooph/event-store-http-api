<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Prooph\EventStore\Http\Api\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
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
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $uri = $request->getUri();
        $uriPath = $uri->getPath();

        $request = $request->withAttribute(self::BASE_URL, $this->baseUrl);

        if ($this->baseUrl !== '/' && strpos($uriPath, $this->baseUrl) === 0) {
            $path = substr($uriPath, strlen($this->baseUrl));
            $path = '/' . ltrim($path, '/');

            $request = $request->withUri($uri->withPath($path));

            $this->urlHelper->setBasePath($this->baseUrl);
        }

        return $delegate->process($request);
    }
}
