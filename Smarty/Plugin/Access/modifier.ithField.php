<?php

function smarty_modifier_ithField($source, $fieldOrRaw = false, bool $raw = false) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithField($source, $fieldOrRaw,  $raw);
}