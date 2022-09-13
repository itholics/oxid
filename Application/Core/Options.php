<?php

namespace ITholics\Oxid\Application\Core;

/**
 * This instance is not really needed for this module as options loader.
 * @inerhitDoc
 */
class Options extends \ITholics\Oxid\Application\Core\Adapter\Options
{
    
    public function getModuleId(): string
    {
        return Module::ID;
    }
}