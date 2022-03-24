<?php 
 namespace App\Services;

use App\Models\PostClass;

class PostClassesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PostClass;       
    }

}