<?php
$file = 'resources/views/pages/hosting/user/templates.blade.php';
$content = file_get_contents($file);

$replacements = [
    'Portfolio+Template' => "href=\"{{ route('user_hosting.template.preview', 'tailwind_portfolio') }}\" target=\"_blank\" class=\"relative",
    'Landing+Page' => "href=\"{{ route('user_hosting.template.preview', 'tailwind_landing') }}\" target=\"_blank\" class=\"relative",
    'Blog+Template' => "href=\"{{ route('user_hosting.template.preview', 'tailwind_blog') }}\" target=\"_blank\" class=\"relative",
    'E-Commerce' => "href=\"{{ route('user_hosting.template.preview', 'tailwind_ecommerce') }}\" target=\"_blank\" class=\"relative",
    'Admin+Dashboard' => "href=\"{{ route('user_hosting.template.preview', 'tailwind_admin') }}\" target=\"_blank\" class=\"relative",
    'Link+in+Bio' => "href=\"{{ route('user_hosting.template.preview', 'tailwind_linkinbio') }}\" target=\"_blank\" class=\"relative",
];

foreach ($replacements as $key => $replacement) {
    // Find the href="javascript:void(0)" ... class="relative that precedes the img with $key
    $pattern = '/href="javascript:void\(0\)" onclick="Swal\.fire[^"]+" class="relative(.*?)(<img src="[^"]+' . preg_quote($key, '/') . ')/s';
    $content = preg_replace($pattern, $replacement . '$1$2', $content);
}

file_put_contents($file, $content);
echo "Updated links in templates.blade.php\n";
