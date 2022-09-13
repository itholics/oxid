<?php

function smarty_modifier_ithOut($source, ?string $moduleId = null): ?string
{
   return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithOut($source, $moduleId);
}