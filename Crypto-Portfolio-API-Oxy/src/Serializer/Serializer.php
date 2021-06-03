<?php
declare(strict_types=1);

namespace App\Serializer;


use App\Entity\Asset;
use App\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Serializer
{
    public function deserialize(string $request, string $class, string $format): Asset|User
    {
        $serializer = new \Symfony\Component\Serializer\Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        return $serializer->deserialize($request, $class, $format);
    }
}

