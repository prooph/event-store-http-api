<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\Http\Api\Model;

use Prooph\EventStore\Metadata\FieldType;
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Metadata\Operator;
use Psr\Http\Message\ServerRequestInterface;

class MetadataMatcherBuilder
{
    public function createMetadataMatcherFrom(ServerRequestInterface $request, bool $includeProperties): MetadataMatcher
    {
        $metadata = [];

        if ($includeProperties) {
            $messageProperty = [];
        }

        foreach ($request->getQueryParams() as $queryParam => $value) {
            $matches = [];

            if (preg_match('/^meta_(\d+)_field$/', $queryParam, $matches)) {
                $metadata[$matches[1]]['field'] = $value;
            } elseif (preg_match('/^meta_(\d+)_operator$/', $queryParam, $matches)
                && defined(Operator::class . '::' . $value)
            ) {
                $metadata[$matches[1]]['operator'] = Operator::byName($value);
            } elseif (preg_match('/^meta_(\d+)_value$/', $queryParam, $matches)) {
                $metadata[$matches[1]]['value'] = $value;
            } elseif ($includeProperties && preg_match('/^property_(\d+)_field$/', $queryParam, $matches)) {
                $messageProperty[$matches[1]]['field'] = $value;
            } elseif ($includeProperties && preg_match('/^property_(\d+)_operator$/', $queryParam, $matches)
                && defined(Operator::class . '::' . $value)
            ) {
                $messageProperty[$matches[1]]['operator'] = Operator::byName($value);
            } elseif ($includeProperties && preg_match('/^property_(\d+)_value$/', $queryParam, $matches)) {
                $messageProperty[$matches[1]]['value'] = $value;
            }
        }

        $metadataMatcher = new MetadataMatcher();

        foreach ($metadata as $key => $match) {
            if (isset($match['field'], $match['operator'], $match['value'])) {
                /** @var Operator $operator */
                $operator = $match['operator'];

                if (($operator->is(Operator::IN()) || $operator->is(Operator::NOT_IN())) && is_string($match['value'])) {
                    $match['value'] = explode(';', $match['value']);
                }

                $metadataMatcher = $metadataMatcher->withMetadataMatch(
                    $match['field'],
                    $match['operator'],
                    $match['value'],
                    FieldType::METADATA()
                );
            }
        }

        if (! $includeProperties) {
            return $metadataMatcher;
        }

        foreach ($messageProperty as $key => $match) {
            if (isset($match['field'], $match['operator'], $match['value'])) {
                $metadataMatcher = $metadataMatcher->withMetadataMatch(
                    $match['field'],
                    $match['operator'],
                    $match['value'],
                    FieldType::MESSAGE_PROPERTY()
                );
            }
        }

        return $metadataMatcher;
    }
}
