<?php

function smarty_modifier_ithUrlFragment($source, ?string $key = null) {
   return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlFragment($source, $key);
}