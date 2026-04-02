<?php

namespace TeamTeaTime\Forum\Actions;

use Illuminate\Support\Facades\DB;
use Throwable;

abstract class BaseAction
{
    abstract protected function transact();

    public function execute()
    {
        DB::beginTransaction();

        try {
            $result = $this->transact();
            DB::commit();

            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
