<?php
$viewsDir = __DIR__ . '/resources/views';
$resources = ['streets', 'villas', 'contributions', 'expenses', 'announcements', 'users'];

foreach ($resources as $resource) {
    // 1. Update index.blade.php
    $indexPath = "$viewsDir/$resource/index.blade.php";
    if (file_exists($indexPath)) {
        $content = file_get_contents($indexPath);
        $content = preg_replace('/(<a[^>]+href="\{\{\s*route\(\''.$resource.'\.create\'\)\s*\}\}"[^>]*)>/', '$1 data-turbo-frame="modal">', $content);
        $content = preg_replace('/(<a[^>]+href="\{\{\s*route\(\''.$resource.'\.edit\',[^}]+\)\s*\}\}"[^>]*)>/', '$1 data-turbo-frame="modal">', $content);
        file_put_contents($indexPath, $content);
    }

    // 2. Update create and edit.blade.php
    foreach (['create', 'edit'] as $action) {
        $path = "$viewsDir/$resource/$action.blade.php";
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (!str_contains($content, '<turbo-frame id="modal">')) {
                // Wrap content inside x-layouts.app with turbo-frame
                $content = preg_replace('/(<x-layouts\.app[^>]*>)/', "$1\n    <turbo-frame id=\"modal\">\n        <div class=\"modal-backdrop\">\n            <div class=\"modal-container\">\n                <button type=\"button\" class=\"modal-close\" onclick=\"document.getElementById('modal').innerHTML=''\">&times;</button>", $content);
                $content = str_replace('</x-layouts.app>', "            </div>\n        </div>\n    </turbo-frame>\n</x-layouts.app>", $content);
                file_put_contents($path, $content);
            }
        }
    }
}
echo "Migration to modals completed.\n";
