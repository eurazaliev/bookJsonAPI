<?php
namespace App\Service;

use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Util\RequestResponseHandler\ViolationUtil;

class RequestChecker
{

    private $serializer;
    private $violator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ViolationUtil $violator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->violator = $violator;
    }

    public function validate(string $data, string $model): object
    {
        if (empty($data))
            throw new BadRequestHttpException('Invalid body.');

        try {
            $object = $this->serializer->deserialize($data, $model, 'json');
        } catch (Exception $e) {
            throw new BadRequestHttpException('Invalid body: ' . $e->getMessage());
        }

        $errors = $this->validator->validate($object);
        $messages = '';
 
        if ($errors->count()) {
            throw new BadRequestHttpException(json_encode($this->violator->build($errors)));
        }

        return $object;
    }
}
