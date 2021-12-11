<?php

namespace Paladin\Api\GraphQL\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TheCodingMachine\GraphQLite\Validator\ValidationFailedException;
use Paladin\Api\GraphQL\Input\Type\AbstractInput;

class AbstractController
{
    public function __construct(protected ServerRequestInterface $request, protected ValidatorInterface $validator)
    {

    }

    /**
     * @param AbstractInput $input
     */
    protected function validateInput(AbstractInput $input)
    {
        $errors = $this->validator->validate($input);

        ValidationFailedException::throwException($errors);
    }
}