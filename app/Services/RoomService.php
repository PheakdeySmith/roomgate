<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\RoomInterface;

class RoomService
{
    protected RoomInterface $roomRepo;

    public function __construct(RoomInterface $roomRepo)
    {
        $this->roomRepo = $roomRepo;
    }

    public function getAll(): Collection
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('admin') || $authUser->hasRole('landlord')) {
            return $this->roomRepo->all();
        }

        abort(403, 'Unauthorized action.');
    }

    public function create(array $data): Room
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('admin')) {
            return $this->roomRepo->create($data);
        }

        if ($authUser->hasRole('landlord')) {
            $data['landlord_id'] = $authUser->id;
            return $this->roomRepo->create($data);
        }

        abort(403, 'Unauthorized action.');
    }

    public function update(int $id, array $data): bool
    {
        $authUser = Auth::user();
        $room = $this->roomRepo->find($id);

        if ($authUser->hasRole('admin')) {
            return $this->roomRepo->update($id, $data);
        }

        if (
            $authUser->hasRole('landlord') &&
            $room->landlord_id === $authUser->id
        ) {
            return $this->roomRepo->update($id, $data);
        }

        abort(403, 'Unauthorized action.');
    }

    public function delete(int $id): bool
    {
        $authUser = Auth::user();
        $room = $this->roomRepo->find($id);

        if ($authUser->hasRole('admin')) {
            return $this->roomRepo->delete($id);
        }

        if (
            $authUser->hasRole('landlord') &&
            $room->landlord_id === $authUser->id
        ) {
            return $this->roomRepo->delete($id);
        }

        abort(403, 'Unauthorized action.');
    }
}
