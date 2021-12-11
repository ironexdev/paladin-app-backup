<?php declare(strict_types=1);

namespace Paladin\Exception\Http;

use Exception;
use JetBrains\PhpStorm\Pure;

abstract class AbstractHttpClientException extends Exception
{
    /**
     * UnprocessableEntityException constructor.
     * @param string $message
     * @param array $data
     * @param null $previous
     */
    #[Pure] public function __construct(string $message = "", protected array $data = [], $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}