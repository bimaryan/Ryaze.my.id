<?php
// generate_previews.php

$filePath = __DIR__ . '/app/Jobs/AutoDeployProject.php';
$content = file_get_contents($filePath);

// We'll extract the HTML for each template and save it to resources/views/previews/
$previewsDir = __DIR__ . '/resources/views/previews';
if (!is_dir($previewsDir)) {
    mkdir($previewsDir, 0755, true);
}

$templates = [
    'tailwind_starter' => 'scaffoldTailwind',
    'tailwind_portfolio' => 'scaffoldTailwindPortfolio',
    'tailwind_landing' => 'scaffoldTailwindLanding',
    'tailwind_blog' => 'scaffoldTailwindBlog',
    'tailwind_ecommerce' => 'scaffoldTailwindEcommerce',
    'tailwind_admin' => 'scaffoldTailwindAdmin',
    'tailwind_linkinbio' => 'scaffoldTailwindLinkinbio',
];

foreach ($templates as $key => $methodName) {
    // Find the method
    $startStr = "private function {$methodName}(string \$dir, string \$name): void";
    $startPos = strpos($content, $startStr);
    if ($startPos === false) continue;
    
    // Find the HTML block inside it
    $htmlStart = strpos($content, '<<<HTML', $startPos);
    if ($htmlStart === false) continue;
    
    $htmlStart += 7; // Length of <<<HTML
    $htmlEnd = strpos($content, "\nHTML", $htmlStart);
    if ($htmlEnd === false) continue;
    
    $htmlContent = substr($content, $htmlStart, $htmlEnd - $htmlStart);
    
    // Replace {$name} with a placeholder name for the preview
    $name = ucwords(str_replace('_', ' ', str_replace('tailwind_', '', $key)));
    if ($key === 'tailwind_starter') $name = 'Tailwind CSS Starter';
    
    $htmlContent = str_replace('{$name}', $name, $htmlContent);
    
    $previewFile = "{$previewsDir}/{$key}.blade.php";
    file_put_contents($previewFile, ltrim($htmlContent));
    echo "Generated preview for {$key}\n";
}

echo "All previews generated successfully!\n";
