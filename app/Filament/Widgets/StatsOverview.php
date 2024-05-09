<?php

namespace App\Filament\Widgets;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Classes', Classes::query()->count()),
            Stat::make('Total Section', Section::query()->count()),
            Stat::make('Total Student', Student::query()->count()),
        ];
    }
}
