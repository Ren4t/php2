<?php

namespace Habr\Renat\UnitTests;

use Psr\Log\LoggerInterface;

class DummyLogger implements LoggerInterface{
    
    public function alert(string|\Stringable $message, mixed $context = []): void {
        
    }

    public function critical(string|\Stringable $message, mixed $context = []): void {
        
    }

    public function debug(string|\Stringable $message, mixed $context = []): void {
        
    }

    public function emergency(string|\Stringable $message, mixed $context = []): void {
        
    }

    public function error(string|\Stringable $message, mixed $context = []): void {
        
    }

    public function info(string|\Stringable $message, mixed $context = []): void {
        
    }

    public function log($level, string|\Stringable $message, mixed $context = []): void {
        
    }

    public function notice(string|\Stringable $message, mixed $context = []): void {
        
    }

    public function warning(string|\Stringable $message, mixed $context = []): void {
        
    }

}
