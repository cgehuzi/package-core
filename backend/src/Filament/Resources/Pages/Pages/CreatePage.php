<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Filament\Resources\Pages\Pages;

use Cgehuzi\Core\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
