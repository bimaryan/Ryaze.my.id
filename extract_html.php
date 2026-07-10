<?php
$content = file_get_contents(__DIR__ . '/app/Jobs/AutoDeployProject.php');

$templates = [
    'tailwind_starter' => 'scaffoldTailwind',
    'tailwind_portfolio' => 'scaffoldTailwindPortfolio',
    'tailwind_landing' => 'scaffoldTailwindLanding',
    'tailwind_blog' => 'scaffoldTailwindBlog',
    'tailwind_ecommerce' => 'scaffoldTailwindEcommerce',
    'tailwind_admin' => 'scaffoldTailwindAdmin',
    'tailwind_linkinbio' => 'scaffoldTailwindLinkinbio',
];

@mkdir(__DIR__ . '/resources/views/previews', 0755, true);

foreach ($templates as $key => $methodName) {
    if (preg_match('/private function ' . $methodName . '.*?<<<HTML(.*?)HTML/s', $content, $matches)) {
        $html = trim($matches[1]);
        $name = 'Preview ' . ucwords(str_replace('_', ' ', $key));
        $html = str_replace('{$name}', $name, $html);
        file_put_contents(__DIR__ . '/resources/views/previews/' . $key . '.blade.php', $html);
        echo "Extracted $key\n";
    }
}
