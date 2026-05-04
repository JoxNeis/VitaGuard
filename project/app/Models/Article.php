<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_username', 'username');
    }

    public function topic()
    {
        return $this->belongsTo(ArticleTopic::class, 'article_topic_id');
    }    
}
