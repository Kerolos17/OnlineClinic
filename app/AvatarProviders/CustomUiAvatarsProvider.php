<?php

namespace App\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class CustomUiAvatarsProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        $name = $this->getUserName($record);
        
        $name = urlencode($name);

        return 'https://ui-avatars.com/api/?name=' . $name . '&color=FFFFFF&background=0ea5e9';
    }

    protected function getUserName(Model | Authenticatable $record): string
    {
        $name = $record->name ?? $record->email ?? 'User';
        
        if (is_array($name)) {
            return $name['en'] ?? $name['ar'] ?? $record->email ?? 'User';
        }
        
        return $name;
    }
}
