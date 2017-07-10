<?php
/**
 * ----------------------------------------------
 * | Автор: Андрей Рыжов (Dune) <info@rznw.ru>   |
 * | Сайт: www.rznw.ru                           |
 * | Телефон: +7 (4912) 51-10-23                 |
 * | Дата: 10.07.2017                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\Waterfall;
use Exception;

class ExceptionCatcher
{
    public function __invoke(Result $result, Exception $e)
    {
        $result->registerException($e);
    }
}