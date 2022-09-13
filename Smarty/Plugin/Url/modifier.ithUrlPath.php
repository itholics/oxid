<?php

function smarty_modifier_ithUrlPath($source, ...$elements) {
   return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlPath($source, ...$elements);
}