<?php

namespace NCSU\GClient\Exceptions;

class MissingCredentialsException extends \Exception
{
    public function report()
    {
        \Log::critical('Google API Credentials are not present');
    }

    public function render()
    {
        parent::render();
    }
}
