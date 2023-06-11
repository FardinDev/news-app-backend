<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "source" => $this['source'],
            "author" => $this['author'],
            "title" => $this['title'],
            "description" => $this['description'],
            "url" => $this['url'],
            "imageUrl" => $this['urlToImage'],
            "publishedAt" => $this['publishedAt'],
            "body" => $this['content']
        ];
    }
}
