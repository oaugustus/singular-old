<?php
namespace Singular;

use Singular\Annotation\Controller;
use Singular\Annotation\Service;
use Singular\Provider\PackServiceProvider;
use \ReflectionClass;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Singular\Register\ControllerRegister;
use Singular\Register\ServiceRegister;

/**
 * Classe Register, registra serviços do pacote.
 *
 * @package Neton
 *
 * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
 */
class Register
{
    /**
     * Registra o namespace padrão das anotações.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        AnnotationRegistry::registerAutoloadNamespace("Singular\Annotation", __DIR__."/../");

        $this->ctrlRegister = new ControllerRegister($app);
        $this->servRegister = new ServiceRegister($app);
    }

    /**
     * Registra o pacote e seus controladores e serviços.
     *
     * @param PackServiceProvider $pack
     */
    public function register(PackServiceProvider $pack)
    {
        $reflector = new ReflectionClass(get_class($pack));
        $packDir = dirname($reflector->getFileName());
        $namespace = $reflector->getNamespaceName();

        $finder = new Finder();

        foreach ($finder->in($packDir)->files() as $file) {

            $className = str_replace(".".$file->getExtension(), '', $file->getRelativePathname());
            $fullClassName = $namespace."\\".str_replace("/","\\",$className);

            if (class_exists($fullClassName)) {
                $this->readAnnotations($fullClassName, $pack);
            }

        }
    }

    /**
     * Extrái as anotações da classe e registra controladores ou serviços.
     *
     * @param string                $class
     * @param PackServiceProvider   $pack
     */
    public function readAnnotations($class, $pack)
    {
        $reader = new AnnotationReader();

        $reflectionClass = new \ReflectionClass($class);
        $classAnnotations = $reader->getClassAnnotations($reflectionClass);

        foreach ($classAnnotations as $annotation) {

            if ($annotation instanceof Controller) {
                $this->ctrlRegister->register($annotation, $reflectionClass, $pack);
            } else
                if ($annotation instanceof Service) {
                    $this->servRegister->register($annotation, $reflectionClass, $pack);
                }

        }

    }
}