<?php

namespace App\Imports;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ArticleImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip empty titles
        if (empty($row['title'])) {
            return null;
        }

        // Get or Create Category
        $categoryId = null;
        if (!empty($row['category'])) {
            $categoryName = trim($row['category']);
            $category = ArticleCategory::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => 'Kategori dibuat otomatis dari import.'
                ]
            );
            $categoryId = $category->id;
        }

        // Generate base slug
        $baseSlug = Str::slug($row['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Get status
        $status = strtolower(trim($row['status'] ?? 'draft'));
        if (!in_array($status, ['draft', 'published', 'archived'])) {
            $status = 'draft';
        }

        // Parse tags
        $tags = null;
        if (!empty($row['tags'])) {
            $tags = array_filter(array_map('trim', explode(',', $row['tags'])));
        }

        return new Article([
            'user_id'          => Auth::id() ?? 1,
            'category_id'      => $categoryId,
            'title'            => $row['title'],
            'slug'             => $slug,
            'excerpt'          => $row['excerpt'] ?? null,
            'body'             => $row['body'] ?? '',
            'tags'             => $tags,
            'status'           => $status,
            'is_featured'      => false,
            'published_at'     => $status === 'published' ? Carbon::now() : null,
            'meta_title'       => \Illuminate\Support\Str::limit(!empty($row['meta_title']) ? $row['meta_title'] : $row['title'], 70, ''),
            'meta_description' => \Illuminate\Support\Str::limit(!empty($row['meta_description']) ? $row['meta_description'] : (!empty($row['excerpt']) ? $row['excerpt'] : strip_tags($row['body'] ?? '')), 160, ''),
        ]);
    }
}
