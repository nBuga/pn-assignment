<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomSerializer
{
    public function serialize(mixed $object): string
    {
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context): string {
                return $object->getName();
            },
        ];
        $normalizer = new ObjectNormalizer(defaultContext: $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);

        return $serializer->serialize($object, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['createdAt', 'updatedAt'],
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            AbstractNormalizer::GROUPS => ['company:station:read1']
        ]);
    }
}