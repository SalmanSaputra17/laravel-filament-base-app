<?php

namespace App\Providers;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        TextColumn::configureUsing(function ($column) {
            if (str_contains($column->getName(), 'at') || $column->getName() === 'created_at' || $column->getName() === 'updated_at') {
                $column->dateTime('M Y, H:i');
            }
        });
    }
}
