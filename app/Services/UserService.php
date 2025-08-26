<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Auth\Access\AuthorizationException; // For throwing authorization exceptions

class UserService
{
    protected UserInterface $userRepo;

    public function __construct(UserInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Get users based on the authenticated user's role.
     * Admin gets all users.
     * Landlord gets users associated with them.
     */
    public function getAll(): Collection
    {
        $authUser = Auth::user();

        if (!$authUser) {
            // This case should ideally be prevented by middleware
            throw new AuthorizationException('Not authenticated.');
        }

        if ($authUser->hasRole('admin')) {
            return $this->userRepo->all(); // Assuming userRepo->all() fetches all necessary details
        }

        if ($authUser->hasRole('landlord')) {
            // You'll need a method in your UserInterface/UserRepository
            // to fetch users specifically for a landlord.
            // Example: return $this->userRepo->getUsersByLandlordId($authUser->id);
            // For now, if 'all()' is meant to be scoped by repository based on context, it might work.
            // However, explicit scoping is safer.
            // If UserInterface->all() is not context-aware for landlords, this needs adjustment.
            // A common pattern is for the repository to handle this scope.
            // For this example, let's assume you'll add a specific method or adjust `all()` behavior.
            // If your repository `all()` method already scopes for landlords when called by one, that's fine.
            // Otherwise, this is a placeholder for correct scoping:
            // return $this->userRepo->getScopedUsersForLandlord($authUser);
            return $this->userRepo->all(); // Placeholder: Adjust if landlords should not see ALL users from repo->all()
        }

        // If the user is not admin or landlord, what should happen?
        // Throwing an exception is better for a service than abort(403).
        throw new AuthorizationException('You are not authorized to view this list of users.');
    }


    /**
     * Create a new user.
     * Handles password hashing and landlord_id assignment for landlords.
     */
    public function create(array $data): User
    {
        $authUser = Auth::user();

        if (!$authUser) {
            throw new AuthorizationException('Not authenticated.');
        }

        // Password hashing
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Policy in controller should have already checked if authUser can create users.
        // Service layer handles role-specific data manipulation.
        if ($authUser->hasRole('landlord')) {
            // Landlord automatically assigns themselves as the landlord_id for the new user.
            $data['landlord_id'] = $authUser->id;
        } elseif (!$authUser->hasRole('admin') && isset($data['landlord_id'])) {
            // If not an admin, a non-landlord user cannot set an arbitrary landlord_id.
            // This logic depends on your business rules. Admins might set it.
            // Landlords set it to themselves. Other roles might not set it at all.
            // This could be refined based on UserPolicy.
            // For now, assume only admin can set it freely or landlord for themselves.
        }
        
        // Ensure 'created_by_user_id' is set if it's part of $data from controller
        // or set it here if it's a service responsibility.
        // $data['created_by_user_id'] = $authUser->id; // Controller already does this

        return $this->userRepo->create($data);
    }

    /**
     * Update an existing user.
     * Accepts a User model instance.
     * Handles password hashing if a new password is provided.
     */
    public function update(User $targetUser, array $data): bool // Changed $id to User $targetUser
    {
        // Authorization should primarily be handled by UserPolicy in the controller.
        // This service method now focuses on the update mechanics.

        // Password hashing if password is being changed
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Ensure password is not accidentally emptied if not provided
            unset($data['password']);
        }
        
        // The controller already sets 'updated_by_user_id'.

        // Pass the ID of the targetUser to the repository's update method.
        // Or, if your repository's update method can accept a User model and data,
        // you could pass $targetUser directly.
        return $this->userRepo->update($targetUser->id, $data);
    }

    /**
     * Delete a user.
     * Accepts a User model instance.
     */
    public function delete(User $targetUser): bool // Changed $id to User $targetUser
    {
        // Authorization should primarily be handled by UserPolicy in the controller.
        // This service method now focuses on the deletion.

        // Pass the ID of the targetUser to the repository's delete method.
        return $this->userRepo->delete($targetUser->id);
    }
}