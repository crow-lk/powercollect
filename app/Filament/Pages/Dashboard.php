<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as Das;


class Dashboard extends Das
{
      /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }
}