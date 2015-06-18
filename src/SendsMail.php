<?php

namespace App\Mailers;

use Mail;
use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
trait SendsMail
{
    /**
     * @param mixed $mailer
     * @return mixed
     */
    protected function mail($mailer)
    {
        return $this->callWithDependencies($mailer, 'mail');
    }

    protected function callWithDependencies($instance, $method)
    {
        return call_user_func_array(
            [$instance, $method], $this->resolveClassMethodDependencies([], $instance, $method)
        );
    }

    protected function resolveClassMethodDependencies(array $parameters, $instance, $method)
    {
        if (!method_exists($instance, $method)) {
            return $parameters;
        }

        return $this->resolveMethodDependencies(
            $parameters, new ReflectionMethod($instance, $method)
        );
    }

    /**
     * Resolve the given method's type-hinted dependencies.
     *
     * @param  array $parameters
     * @param  \ReflectionFunctionAbstract $reflector
     * @return array
     */
    public function resolveMethodDependencies(array $parameters, ReflectionFunctionAbstract $reflector)
    {
        foreach ($reflector->getParameters() as $key => $parameter) {
            $class = $parameter->getClass();
            if ($class && !$this->alreadyInParameters($class->name, $parameters)) {
                array_splice(
                    $parameters, $key, 0, [app()->make($class->name)]
                );
            }
        }

        return $parameters;
    }

    /**
     * Determine if an object of the given class is in a list of parameters.
     *
     * @param  string $class
     * @param  array $parameters
     * @return bool
     */
    protected function alreadyInParameters($class, array $parameters)
    {
        return !is_null(array_first($parameters, function ($key, $value) use ($class) {
            return $value instanceof $class;
        }));
    }
}
