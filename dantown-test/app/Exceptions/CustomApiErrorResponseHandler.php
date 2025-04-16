<?php


class CustomApiErrorResponseHandler extends Exception
{
    protected $statusCode;
    protected $message;
    protected $redirectUrl;

    public function __construct($message, $statusCode = 400, $url = null)
    {
        $this->redirectUrl = $url;
        $this->statusCode = $statusCode;
        $this->message = $message;
    }
}