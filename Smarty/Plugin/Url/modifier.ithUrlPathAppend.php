<?php

function smarty_modifier_ithUrlPathAppend($source, ...$elements) {
   return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlPathAppend($source, ...$elements);
}