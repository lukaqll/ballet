namespace App\Services;

use App\Models\{{ $model }};

class {{ $name }} extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new {{ $model }};       
    }

}