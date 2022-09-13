<?php

function smarty_function_ithDebug($params, &$smarty) {
    return '<pre>' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>';
}