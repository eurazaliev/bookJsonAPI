<?php
namespace App\Service;

use Exception;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class MySerializer
{
    private $encoders;
    private $classMetadataFactory;

    public function __construct() {
        $this->encoders = [new JsonEncoder()];
        $this->classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    }

    public function getSerializer(): SerializerInterface
    {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                    return $object->getName();
            },
        ];
        $normalizers = [new ObjectNormalizer($this->classMetadataFactory, null, null, null, null, null, $defaultContext)];

        return new Serializer($normalizers, $this->encoders);
    }
}
