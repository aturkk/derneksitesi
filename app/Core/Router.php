<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string, regex:string, handler:array}> */
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    private function map(string $method, string $path, array $handler): void
    {
        $pattern = preg_replace('~\{([a-zA-Z_][a-zA-Z0-9_]*)\}~', '(?P<$1>[^/]+)', $path);
        $this->routes[] = [
            'method'  => $method,
            'regex'   => '~^' . $pattern . '$~u',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $uri;
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }
        $path = rawurldecode($path);

        // Uygulama alt klasörde çalışıyorsa taban yolu soy
        $base = base_url();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        if ($path === '' || $path === false) {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[] = $value;
                    }
                }
                [$class, $action] = $route['handler'];
                $controller = new $class();
                $result = $controller->{$action}(...$params);
                if (is_string($result)) {
                    echo $result;
                }
                return;
            }
        }

        http_response_code(404);
        echo (new \App\Controllers\ErrorController())->notFound();
    }
}
