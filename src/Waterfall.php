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
use Exception as ExceptionBase;

class Waterfall
{
    protected $drops = [];

    /**
     * @var Drop|null
     */
    protected $finalDrop = null;

    protected $errorHandler = null;

    protected $dropsPriority = [];

    protected $exceptionCatcher = null;

    public function addDrop(Drop $drop, $priority = 100)
    {
        $name = $drop->getName();
        $this->drops[$name] = $drop;
        $this->dropsPriority[$name] = $priority;
        return $this;
    }

    public function setFinalDrop(Drop $drop)
    {
        $this->finalDrop = $drop;
        return $this;
    }

    public function setErrorHandler(Drop $drop)
    {
        $this->errorHandler = $drop;
        return $this;
    }

    public function removeDrop($dropName)
    {
        if (array_key_exists($dropName, $this->drops)) {
            unset($this->drops[$dropName]);
            unset($this->dropsPriority[$dropName]);
        }
        return $this;
    }

    public function getDrop($dropName)
    {
        if (array_key_exists($dropName, $this->drops)) {
            return $this->drops[$dropName];
        }
        throw new Exception('No drop by name: ' . $dropName, 101);
    }


    public function getDrops()
    {
        return $this->drops;
    }

    public function getDropsPriority()
    {
        return $this->dropsPriority;
    }

    public function setExceptionCatcher($catcher)
    {
        if (!is_callable($catcher)) {
            throw new Exception('Exception сatcher mast have __invoke method', 100);
        }
        $this->exceptionCatcher = $catcher;
        return $this;
    }

    public function execute()
    {
        $result = new Result();
        $priority = $this->getSortedPriority();

        try {
            $error = false;
            foreach ($priority as $dropName => $dropPriority) {
                /** @var Drop $drop */
                $drop = $this->drops[$dropName];
                $drop->execute($result);
                if ($result->isStopped()) {
                    break;
                }
                if ($error = $result->getError()) {
                    break;
                }
            }
            if ($error) {
                if ($this->errorHandler) {
                    $this->errorHandler->execute($result);
                }
            } else if ($this->finalDrop) {
                $this->finalDrop->execute($result);
            }

        } catch (ExceptionBase $e) {
            if ($this->exceptionCatcher) {
                $exceptionCatcher = $this->exceptionCatcher;
            }  else {
                $exceptionCatcher = new ExceptionCatcher();
            }
            $exceptionCatcher($result, $e);
        }
        return $result;
    }

    protected function getSortedPriority()
    {
        $priority = $this->dropsPriority;
        return asort($priority, SORT_NUMERIC );
    }
}