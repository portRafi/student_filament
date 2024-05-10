<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class GenerateQrCode extends Page
{
    use InteractsWithRecord;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.generate-qr-code';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
    }

    public function getModel(): string
    {
        return parent::getModel();
    }

    private function resolveRecord(int|string $record)
    {
    }
}
