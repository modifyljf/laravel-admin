<?php

namespace Guesl\Admin\Exceptions;

use Exception;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2017/4/16
 */
class AuthException extends Exception
{
    protected $key;

    protected $attributes;

    protected $errorBag;

    /**
     * Create a new authentication exception.
     *
     * @param string $key
     * @param  string $message
     * @param string $errorBag
     * @param int $code
     */
    public function __construct($key, $message = 'Business Exception.', $errorBag = "default", $code = 422)
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
