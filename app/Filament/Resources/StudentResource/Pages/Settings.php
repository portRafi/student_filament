<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;

class Settings extends Page
{
    public $defaultActionArguments = ['step' => 2];

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.settings';

    protected function getHeaderActions(): array
{
    return [
        Action::make('edit')
            ->url(route('posts.edit', ['post' => $this->post])),
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->post->delete()),
    ];
}
}
