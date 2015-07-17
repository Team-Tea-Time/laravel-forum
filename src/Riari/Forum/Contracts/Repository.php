<?php namespace Riari\Forum\Contracts;

interface Repository
{
    public function all($columns = ['*']);

    public function paginate($columns = ['*']);

    public function create(array $data);

    public function update($id = 0, array $data);

    public function delete($id = 0);

    public function find($id = 0, $columns = ['*']);

    public function findBy($column = '', $value, $columns = ['*']);
}
