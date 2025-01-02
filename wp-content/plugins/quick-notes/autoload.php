<?php
const DS = DIRECTORY_SEPARATOR;
spl_autoload_register(function (string $class): void {

    $baseNamespace = 'WBZXTDL\\';

    if (str_starts_with($class, $baseNamespace)) {

        $relativeClass = substr($class, strlen($baseNamespace));

        $file = __DIR__ . DS. str_replace('\\', DS, $relativeClass) . '.php';


        if (is_file($file)) {
            require $file;
        }
    }
});