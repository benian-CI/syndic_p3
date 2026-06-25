<?php
$cssFile = __DIR__ . '/resources/css/app.css';
$content = file_get_contents($cssFile);

$sections = preg_split('/(\/\* ={60}\s+.*?\s+={60} \*\/)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

$appCssContent = [];
$componentsContent = [];
$layoutContent = [];
$baseContent = [];
$variablesContent = [];
$modalsContent = [];

// The first element is the preamble before the first comment block
$appCssContent[] = trim($sections[0]);

for ($i = 1; $i < count($sections); $i += 2) {
    $header = $sections[$i];
    $body = $sections[$i + 1] ?? '';
    
    $fullSection = $header . "\n" . trim($body) . "\n\n";
    
    if (str_contains($header, 'DESIGN TOKENS')) {
        $variablesContent[] = $fullSection;
    } elseif (str_contains($header, 'RESET & BASE') || str_contains($header, 'TYPOGRAPHIE') || str_contains($header, 'GRILLES')) {
        $baseContent[] = $fullSection;
    } elseif (str_contains($header, 'LAYOUT') || str_contains($header, 'SIDEBAR') || str_contains($header, 'TOPBAR')) {
        $layoutContent[] = $fullSection;
    } elseif (str_contains($header, 'MODALS')) {
        $modalsContent[] = $fullSection;
    } else {
        // Boutons, Cards, Tables, Forms, Alertes, Pagination, Login, Charts, etc.
        $componentsContent[] = $fullSection;
    }
}

$dir = __DIR__ . '/resources/css';

file_put_contents("$dir/variables.css", implode("", $variablesContent));
file_put_contents("$dir/base.css", implode("", $baseContent));
file_put_contents("$dir/layout.css", implode("", $layoutContent));
file_put_contents("$dir/components.css", implode("", $componentsContent));
file_put_contents("$dir/modals.css", implode("", $modalsContent));

$newAppCss = implode("\n\n", $appCssContent) . "\n\n";
$newAppCss .= "@import './variables.css';\n";
$newAppCss .= "@import './base.css';\n";
$newAppCss .= "@import './layout.css';\n";
$newAppCss .= "@import './components.css';\n";
$newAppCss .= "@import './modals.css';\n";

// Need to handle body transition styling if it's placed outside blocks.
// Wait, in my previous edit, I added body transition outside the block. Let's make sure it's caught or just put it in base.
$newAppCss = preg_replace("/\nbody \{(.*?)\}/s", "", $newAppCss);
$baseContent[] = "body {\n    transition: background-color var(--transition-base), color var(--transition-base);\n}\n";
file_put_contents("$dir/base.css", implode("", $baseContent));

file_put_contents($cssFile, $newAppCss);
echo "CSS Refactoring Complete.\n";
