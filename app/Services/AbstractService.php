<?php
namespace App\Services;

abstract class AbstractService{

    protected $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    private function resolveModel()
    {
        return app( $this->model );
    }



    private function findColumn( $columnName )
    {
        $isColExists = $this->model->getConnection()
                                    ->getSchemaBuilder()
                                    ->hasColumn($this->model->getTable(), $columnName);
        return $isColExists;
    }
    
    /**
     * create method
     * 
     * @param array $data
     * 
     * @return $this->model
     */
    public function create( array $data )
    {
        return $this->model->create( $data );
    }

    /**
     * get by id
     * 
     * @param int $id
     * 
     * @return $this->model
     */
    public function find( $id )
    {
        return $this->model->find( intval( $id ) );
    }

    /**
     * update by id method
     * 
     * @param array $data
     * @param int $id
     * 
     * @return $this->model
     */
    public function updateById( $id, array $data )
    {
        $finded = $this->find( intval( $id ) );
        $finded->update( $data );
        return $finded;
    }

    /**
     * update by id method
     * 
     * @param object $model
     * @param int $id
     * 
     * @return bool
     */
    public function update( array $data )
    {
        $this->model->update( $data );
        return $this->model;
    }

    /**
     * get list by array
     * 
     * @param array $filters
     * 
     * @return $this->model
     */
    public function list( array $filters = [], $orderings = [], $order = 'asc' )
    {
     
        $filterable = $this->model;

        foreach( $filters as $key => $value ){

            if( $value != '' )
                $filterable = $filterable->where( $key, $value );
        }

        
        //only not deleted
        if( $this->findColumn('deleted') ){
            $filterable = $filterable->where('deleted', '!=', 1);
        }

        foreach($orderings as $ordering)
            $filterable = $filterable->orderBy( $ordering, $order );
        

        return $filterable->get();
    }

    /**
     * get list by array
     * 
     * @param array $filters
     * 
     * @return $this->model
     */
    public function adminList( array $filters = [], $ordering = false, $order = 'asc' )
    {
     
        $filterable = $this->model;

        foreach( $filters as $key => $value ){

            if( $value != '' )
                $filterable = $filterable->where( $key, $value );
        }

        if( $ordering ){
            $filterable = $filterable->orderBy( $ordering, $order );
        }

        return $filterable->get();
    }

    /**
     * get frist by array
     * 
     * @param array $data
     * 
     * @return $this->model
     */
    public function get( array $data = [] )
    {
        $findable =  $this->model->where( $data );

        if( $this->findColumn('deleted') ){
            $findable = $findable->where('deleted', '!=', 1);
        }

        return $findable->first();
    }

    /**
     * get frist by array
     * 
     * @param array $data
     * 
     * @return $this->model
     */
    public function adminGet( array $data = [] )
    {
        $findable =  $this->model->where( $data );

        return $findable->first();
    }

    /**
     * delete by id method
     * 
     * @param array $data
     * @param int $id
     * 
     * @return $this->model
     */
    public function deleteById( $id )
    {
        $finded = $this->find( intval( $id ) );
        $finded->delete();
        return $finded;
    }


}