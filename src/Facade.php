<?php

namespace NCSU\GClient;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GClient';
    }
}
