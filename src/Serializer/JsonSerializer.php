<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Sdk\Paket\Retoure\Serializer;

/**
 * JsonSerializer
 *
 * Serializer for outgoing request types and incoming responses.
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class JsonSerializer
{
    public function encode(\JsonSerializable $request)
    {
        // remove empty entries from serialized data (after all objects were converted to array)
        $payload = (string) \json_encode($request);
        $payload = (array) \json_decode($payload, true);
        $payload = $this->filterRecursive($payload);

        return (string) \json_encode($payload);
    }

    /**
     * Recursively filter null and empty strings from the given (nested) array
     *
     * @param mixed[] $element
     * @return mixed[]
     */
    private function filterRecursive(array $element)
    {
        // Filter null and empty strings
        $filterFunction = static function ($entry) {
            return ($entry !== null) && ($entry !== '') && ($entry !== []);
        };

        foreach ($element as &$value) {
            if (\is_array($value)) {
                $value = $this->filterRecursive($value);
            }
        }

        return array_filter($element, $filterFunction);
    }

    /**
     * @param string $jsonResponse
     * @return string[]
     */
    public function decode($jsonResponse)
    {
        return \json_decode($jsonResponse, true);
    }
}
