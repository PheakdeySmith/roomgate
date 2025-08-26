<?php

namespace App\Repositories\Interfaces;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;

interface RoomInterface
{
    public function all(): Collection;
    public function find(int $id): Room;
    public function create(array $data): Room;
    public function update(int $id, array $data): bool;
    public function delete(int $id): int;
}
