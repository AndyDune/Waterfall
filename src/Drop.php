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
use Interop\Container\ContainerInterface as InteropContainerInterface;

class Drop
{
    protected $name;

    protected $serviceManager = null;

    protected $function;

    public function __construct()
    {
        $this->name = uniqid();
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setCallable($function)
    {
        $this->function = $function;
        return $this;
    }

    public function setInvokable($class, $function = null)
    {
        $function = function(Result $result) use ($class, $function) {
            $object = new $class;
            if ($function) {
                call_user_func([$object, $function], $result);
            } else {
                call_user_func($object, $result);
            }
        };
        $this->function = $function;
        return $this;
    }

    public function setService($serviceName, $function = null)
    {
        $self = $this;
        $function = function(Result $result) use ($serviceName, $function, $self) {
            if (!$this->serviceManager) {
                throw new Exception('No service manager set. Use method setServiceManager');
            }

            if (!$this->serviceManager->has($serviceName)) {
                throw new Exception('No service by name: ' . $serviceName);
            }

            $object = $this->serviceManager->get($serviceName);
            if ($function) {
                call_user_func([$object, $function], $result);
            } else {
                call_user_func($object, $result);
            }
        };
        $this->function = $function;
        return $this;
    }

    public function setServiceManager(InteropContainerInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }


    public function execute(Result $result)
    {

    }
}