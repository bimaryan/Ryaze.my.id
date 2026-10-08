<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public Navigation Links
    |--------------------------------------------------------------------------
    | Single source of truth for public navigation links.
    | Used by both React (PublicLayout.jsx) and Blade (public.blade.php).
    */
    'links' => [
        ['label' => 'Tentang', 'href' => '/#about'],
        ['label' => 'Layanan', 'href' => '/#services'],
        ['label' => 'Harga', 'href' => '/#pricing'],
        ['label' => 'Cek Domain', 'href' => '/whois', 'route' => 'whois.index'],
        
        ['label' => 'Blog', 'href' => '/blog', 'route' => 'blog.index'],
    ],

    'footer_links' => [
        ['label' => 'Tentang', 'href' => '/#about'],
        ['label' => 'Layanan', 'href' => '/#services'],
        ['label' => 'Blog', 'href' => '/blog'],
        ['label' => 'Privasi', 'href' => '/privacy'],
        ['label' => 'Syarat', 'href' => '/terms'],
    ],
];