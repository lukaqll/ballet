<?php 
 namespace App\Services;

use App\Models\Post;

class PostsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Post;       
    }

}