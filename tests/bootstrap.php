<?php
declare(strict_types=1);

$projectRoot = dirname(__DIR__);

spl_autoload_register(static function (string $class) use ($projectRoot): void {
    if (str_starts_with($class, 'Aether\\Modules\\')) {
        if (preg_match('/^Aether\\\\Modules\\\\([^\\\\]+)\\\\(.+)$/', $class, $matches) === 1) {
            $file = $projectRoot . '/src/Aether/Modules/' . $matches[1] . '/src/' . str_replace('\\', '/', $matches[2]) . '.php';
            if (is_file($file)) {
                require_once $file;
            }
        }
        return;
    }

    if (str_starts_with($class, 'Aether\\')) {
        $file = $projectRoot . '/src/' . str_replace('\\', '/', $class) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
        return;
    }

    if (str_starts_with($class, 'App\\')) {
        $file = $projectRoot . '/app/' . str_replace('\\', '/', $class) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
        return;
    }

    if (str_starts_with($class, 'Tests\\')) {
        $file = $projectRoot . '/tests/' . str_replace('\\', '/', substr($class, 6)) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});

require_once $projectRoot . '/src/Aether/Utils/CoreHelperFunctions.php';
