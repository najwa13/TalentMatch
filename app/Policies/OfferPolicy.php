<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Offer $offer): bool
    {
        return (int) $user->id === (int) $offer->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Offer $offer): bool
    {
        return (int) $user->id === (int) $offer->user_id;
    }

    public function delete(User $user, Offer $offer): bool
    {
        return (int) $user->id === (int) $offer->user_id;
    }
}
