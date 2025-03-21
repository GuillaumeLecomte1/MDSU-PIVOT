<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Vérifier si nous sommes dans un environnement Docker/production
        $this->app->singleton('log', function ($app) {
            try {
                // Logger pour le diagnostic
                error_log("[PIVOT] Initialisation du système de logs personnalisé");
                
                // Créer un logger qui écrit sur stderr
                $stderrLogger = new Logger('stderr');
                $stderrLogger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));
                
                // Pour le diagnostic, on veut voir toutes les erreurs
                return new class($stderrLogger) {
                    private $logger;
                    
                    public function __construct($logger)
                    {
                        $this->logger = $logger;
                    }
                    
                    /**
                     * Intercepte tous les appels de méthode et les redirige vers stderr
                     */
                    public function __call($method, $parameters)
                    {
                        if (in_array($method, ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'])) {
                            $message = isset($parameters[0]) ? $parameters[0] : 'No message';
                            error_log("[PIVOT-$method] $message");
                            $this->logger->$method($message, $parameters[1] ?? []);
                        }
                        return $this;
                    }
                    
                    // Méthodes explicites pour le diagnostic
                    public function emergency($message, array $context = []): self
                    {
                        error_log("[PIVOT-EMERGENCY] $message");
                        $this->logger->emergency($message, $context);
                        return $this;
                    }
                    
                    public function alert($message, array $context = []): self
                    {
                        error_log("[PIVOT-ALERT] $message");
                        $this->logger->alert($message, $context);
                        return $this;
                    }
                    
                    public function critical($message, array $context = []): self
                    {
                        error_log("[PIVOT-CRITICAL] $message");
                        $this->logger->critical($message, $context);
                        return $this;
                    }
                    
                    public function error($message, array $context = []): self
                    {
                        error_log("[PIVOT-ERROR] $message");
                        $this->logger->error($message, $context);
                        return $this;
                    }
                    
                    public function warning($message, array $context = []): self
                    {
                        error_log("[PIVOT-WARNING] $message");
                        $this->logger->warning($message, $context);
                        return $this;
                    }
                    
                    public function notice($message, array $context = []): self
                    {
                        error_log("[PIVOT-NOTICE] $message");
                        $this->logger->notice($message, $context);
                        return $this;
                    }
                    
                    public function info($message, array $context = []): self
                    {
                        error_log("[PIVOT-INFO] $message");
                        $this->logger->info($message, $context);
                        return $this;
                    }
                    
                    public function debug($message, array $context = []): self
                    {
                        error_log("[PIVOT-DEBUG] $message");
                        $this->logger->debug($message, $context);
                        return $this;
                    }
                    
                    public function log($level, $message, array $context = []): self
                    {
                        error_log("[PIVOT-LOG] [$level] $message");
                        $this->logger->log($level, $message, $context);
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
            } catch (\Throwable $e) {
                // En cas d'erreur, on log l'erreur et on retourne un logger de secours
                error_log("[PIVOT-CRITICAL] Erreur lors de l'initialisation du logger: " . $e->getMessage());
                $fallbackLogger = new Logger('fallback');
                $fallbackLogger->pushHandler(new NullHandler());
                return $fallbackLogger;
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Force l'utilisation de stderr comme canal par défaut
            config(['logging.default' => 'stderr']);
            
            // Assure que le canal stderr existe
            config(['logging.channels.stderr' => [
                'driver' => 'monolog',
                'handler' => StreamHandler::class,
                'with' => [
                    'stream' => 'php://stderr',
                    'level' => config('app.debug') ? Logger::DEBUG : Logger::ERROR,
                ],
            ]]);
            
            error_log("[PIVOT] LoggingServiceProvider démarré avec succès");
        } catch (\Throwable $e) {
            error_log("[PIVOT-CRITICAL] Erreur dans LoggingServiceProvider::boot: " . $e->getMessage());
        }
    }
} 