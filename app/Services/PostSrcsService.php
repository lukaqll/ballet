<?php 
 namespace App\Services;

use App\Models\PostSrc;

class PostSrcsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PostSrc;       
    }

}