<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Créer un logger null personnalisé qui remplace le logger standard
        $this->app->singleton('log', function () {
            $nullLogger = new Logger('null');
            $nullLogger->pushHandler(new NullHandler());
            
            return new class($nullLogger) {
                private $logger;
                
                public function __construct($logger)
                {
                    $this->logger = $logger;
                }
                
                /**
                 * Intercepte tous les appels de méthode et ne fait rien
                 */
                public function __call($method, $parameters)
                {
                    return $this;
                }
                
                /**
                 * Les méthodes les plus couramment utilisées retournent l'instance
                 */
                public function emergency($message, array $context = []): self
                {
                    return $this;
                }
                
                public function alert($message, array $context = []): self
                {
                    return $this;
                }
                
                public function critical($message, array $context = []): self
                {
                    return $this;
                }
                
                public function error($message, array $context = []): self
                {
                    return $this;
                }
                
                public function warning($message, array $context = []): self
                {
                    return $this;
                }
                
                public function notice($message, array $context = []): self
                {
                    return $this;
                }
                
                public function info($message, array $context = []): self
                {
                    return $this;
                }
                
                public function debug($message, array $context = []): self
                {
                    return $this;
                }
                
                public function log($level, $message, array $context = []): self
                {
                    return $this;
                }
                
                public function channel($channel = null): self
                {
                    return $this;
                }
                
                public function stack(array $channels, $channel = null): self
                {
                    return $this;
                }
                
                public function getLogger()
                {
                    return $this->logger;
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Force l'utilisation du canal null, même si un autre est configuré
        config(['logging.default' => 'null']);
        
        // S'assurer que le canal null existe
        config(['logging.channels.null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ]]);
    }
} 