<?php

namespace App\Repositories\Interfaces;

use App\Models\Event;

interface EventRepositoryInterface
{
    public function paginate();
    public function find(int $id): ?Event;
    public function create(array $data): Event;
    public function delete(int $id): bool;
}