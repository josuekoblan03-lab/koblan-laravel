<?php
$viewsDir = __DIR__ . '/resources/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        if (strpos($content, 'DOMContentLoaded') !== false) {
            $content = str_replace("document.addEventListener('DOMContentLoaded',", "document.addEventListener('turbo:load',", $content);
            $content = str_replace('document.addEventListener("DOMContentLoaded",', "document.addEventListener('turbo:load',", $content);
            file_put_contents($path, $content);
            $count++;
            echo "Updated: " . $file->getFilename() . "\n";
        }
    }
}
echo "Total updated: $count\n";
