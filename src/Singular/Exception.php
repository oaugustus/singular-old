<?php
/**
 * Este arquivo é parte do Singular Framework.
 *
 * (c) Otávio Fernandes <otavio@netonsolucoes.com.br>
 */

namespace Singular;

/**
 * Classe responsável pelo tratamento de exceções do framework.
 *
 * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
 */
class Exception extends \Exception
{
    /**
     * Cria uma nova FrameworkException descrevendo a falta de configuração do diretório de aplicação.
     *
     * @param string $message Mensagem da exceção
     *
     * @return FrameworkException
     */
    public static function baseDirNotFoundError($message)
    {
        return new self('[Base Directory Not Found Error] ' . $message);
    }

    public static function directoryNotFound($message)
    {
        return new self('[Directory Not Found Error] '.$message);
    }

    /**
     * Cria uma nova FrameworkException descrevendo a falta de configuração do diretório de arquivos de configuração.
     *
     * @param string $message Mensagem da exceção
     *
     * @return FrameworkException
     */
    public static function configDirNotFoundError($message)
    {
        return new self('[Config Directory Not Found Error] ' . $message);
    }

    /**
     * Cria uma nova FrameworkException descrevendo que os bundles da aplicação não foram definidos.
     *
     * @param string $message Mensagem de exceção
     *
     * @return FrameworkException
     */
    public static function bundlesNotDefinedError($message)
    {
        return new self('[Bundle Not Defined Error] ' . $message);
    }

    /**
     * Cria uma nova FrameworkException descrevendo que um método de um controlador não foi definido.
     *
     * @param string $message Mensagem de exceção
     *
     * @return FrameworkException
     */
    public static function controllerMethodNotDefinedError($message)
    {
        return new self('[Controller Method Not Defined Error] ' . $message);
    }

    /**
     * Cria uma nova FrameworkException descrevendo que um método de um controlador não foi definido.
     *
     * @param string $message Mensagem de exceção
     *
     * @return FrameworkException
     */
    public static function filterHasAnnotationError($message)
    {
        return new self('[Filter Has Annotation Error] ' . $message);
    }

    /**
     * Cria uma nova FrameworkException descrevendo que um método anotado como rota não possui um pattern definido.
     *
     * @param string $message Mensagem de exceção
     *
     * @return FrameworkException
     */
    public static function routePatternNotDefinedError($message)
    {
        return new self('[Route Pattern Not Defined Error] ' . $message);
    }

    /**
     * Cria uma nova FrameworkException descrevendo que método anotado como rota não possui um método definido.
     *
     * @param string $message Mensagem de exceção
     *
     * @return FrameworkException
     */
    public static function routeMethodNotDefinedError($message)
    {
        return new self('[Route Method Not Defined Error] ' . $message);
    }
}