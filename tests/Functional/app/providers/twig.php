<?php
/**
 * Registra o provedor de serviços do twig.
 *
 * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
 */
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => $app['base_dir']."/views"
));