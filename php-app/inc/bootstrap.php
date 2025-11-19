<?php
// Lightweight autoloader to ensure class definitions are available before session unserialize
spl_autoload_register(function ($class) {
    // Try model/ then inc/ directories
    $paths = [__DIR__ . '/../model/', __DIR__ . '/'];
    $file = str_replace('\\', '/', $class) . '.php';
    foreach ($paths as $p) {
        $candidate = $p . $file;
        if (file_exists($candidate)) {
            require_once $candidate;
            return true;
        }
    }
    return false;
});

// Optionally require known classes immediately (uncomment if you prefer explicit load)
// require_once __DIR__ . '/../model/User.php';

?>
