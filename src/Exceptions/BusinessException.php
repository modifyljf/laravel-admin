<?php

namespace Guesl\Admin\Exceptions;

use Exception;

/**
 * Class BusinessException
 * @package Guesl\Admin\Exceptions
 */
class BusinessException extends Exception
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected array $attributes;

    /**
     * @var string
     */
    protected string $errorBag;

    /**
     * Create a new authentication exception.
     *
     * @param string $key
     * @param string $message
     * @param string $errorBag
     * @param int $code
     */
    public function __construct(string $key, $message = 'Business Exception.', $errorBag = "default", int $code = 422)
    {
        parent::__construct($message, $code);
        $this->key = $key;
        $this->errorBag = $errorBag;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getErrorBag()
    {
        return $this->errorBag;
    }

    /**
     * @param string $errorBag
     */
    public function setErrorBag($errorBag)
    {
        $this->errorBag = $errorBag;
    }
}
