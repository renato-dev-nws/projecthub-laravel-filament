<?php

namespace App\Filament\TeamPanel\Resources\Users\Pages;

use App\Filament\TeamPanel\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
