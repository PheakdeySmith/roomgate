<?php

namespace App\Repositories;

use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RoomInterface;

class RoomRepository implements RoomInterface
{
    public function all(): Collection
    {
        $user = Auth::user();

        $query = Room::with('landlord');

        if ($user) {
            if ($user->hasRole('admin')) {
                return $query->get();
            }

            if ($user->hasRole('landlord')) {
                return $query->where('landlord_id', $user->id)->get();
            }
        }

        return collect();
    }

    public function find(int $id): Room
    {
        $user = Auth::user();
        $target = Room::with('landlord')->findOrFail($id);

        if ($user->hasRole('admin')) {
            return $target;
        }

        if ($user->hasRole('landlord') && $target->landlord_id === $user->id) {
            return $target;
        }

        abort(404, 'User not found or unauthorized.');
    }

    public function create(array $data): Room
    {
        $user = Auth::user();

        if ($user->hasRole('landlord')) {
            $data['landlord_id'] = $user->id;
        }

        return Room::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $target = $this->find($id);

        return $target->update($data);
    }

    public function delete(int $id): int
    {
        $target = $this->find($id);

        return $target->delete();
    }
}
