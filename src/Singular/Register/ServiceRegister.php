<?php
namespace Singular\Register;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Singular\Annotation\Service;
use Singular\Provider\PackServiceProvider;
use Singular\Application;

/**
 * Class ServiceRegister
 *
 * @package Neton
 *
 * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
 */
class ServiceRegister
{
    /**
     * Registra o container de dependência.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->reader = new AnnotationReader();
    }

    /**
     * Registra os serviços do pacote.
     *
     * @param Service               $annotation
     * @param \ReflectionClass      $reflectionClass
     * @param PackServiceProvider   $pack
     */
    public function register($annotation, $reflectionClass, $pack)
    {
        $app = $this->app;
        $relativeNamespace = preg_replace('/'.$pack->getNameSpace().'/', '', $reflectionClass->getName(),1);
        $serviceKey = $pack->getPackName().strtolower(implode('.',explode('\\',$relativeNamespace)));
        $class = $reflectionClass->getName();

        switch ($annotation->type) {
            case 'clousure':
                $app[$serviceKey] = function () use ($class, $app, $pack) {
                    return new $class($app, $pack->getPackName());
                };
                break;
            case 'shared':
                $app[$serviceKey] = $app->share(function () use ($class, $app, $pack) {
                    return new $class($app, $pack->getPackName());
                });
                break;
            case 'protected':
                $app[$serviceKey] = $app->protect(function () use ($class, $app, $pack) {
                    return new $class($app, $pack->getPackName());
                });
                break;
        }

        $this->registerParameters($reflectionClass, $serviceKey);
    }

    /**
     * Registra parâmetros definidos dentro dos serviços.
     *
     * @param \ReflectionClass  $reflectionClass
     * @param string            $serviceKey
     */
    public function registerParameters($reflectionClass, $serviceKey)
    {
        $app = $this->app;
        $properties = $reflectionClass->getProperties();
        $class = $reflectionClass->getName();

        foreach ($properties as $property) {

            $annotation = $this->reader->getPropertyAnnotation($property, 'Singular\Annotation\Parameter');

            if (!empty($annotation)) {

                $parameterKey = $serviceKey.".".$property->getName();

                $app[$parameterKey] = function () use ($class, $property) {

                    $prop = $property->getName();

                    return $class::$$prop;
                };
            }
        }
    }
} 