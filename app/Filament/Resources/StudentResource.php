<?php

namespace App\Filament\Resources;

use App\Exports\StudentExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Students Academic ';

    public static function getNavigationBadge(): ?string
    {
        return static::$model::query()->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('class_id')
                    ->live()
                    ->relationship('class', 'name'),
                Forms\Components\Select::make('section_id')
                    ->label('Section')
                    ->options(function (Forms\Get $get) {
                        $classId = $get('class_id');

                        if ($classId) {
                            return Section::query()
                                ->where('class_id', $classId)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                    }),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->unique()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('class.name')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section.name')
                    ->badge()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('class-section-filter')
                    ->form([
                        Forms\Components\Select::make('class_id')
                            ->label('Filter By Class')
                            ->placeholder('Select a Class')
                            ->options([
                                Classes::query()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            ]),
                        Forms\Components\Select::make('section_id')
                            ->label('Filter By Section')
                            ->placeholder('Select a Section')
                            ->options(function (Forms\Get $get) {
                                $classId = $get('class_id');

                                if ($classId) {
                                    return Section::query()
                                        ->where('class_id', $classId)
                                        ->pluck('name', 'id');
                                }
                            })
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['class_id'], function ($query) use ($data) {
                            return $query->where('class_id', $data['class_id']);
                        })->when($data['section_id'], function ($query) use ($data) {
                            return $query->where('section_id', $data['section_id']);
                        });
                    }),
            ])
            ->actions([
                Action::make('downloadPdf')
                    ->url(function (Student $student) {
                        return route('student.invoice.generate', $student);
                    }),
                Action::make('qrCode')
                    ->url(function (Student $record) {
                        return static::getUrl('qrCode', ['record' => $record]);
                    }),
                Tables\Actions\EditAction::make(),
                
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export to Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            return Excel::download(new StudentExport($records), 'students.xlsx');
                        })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'qrCode' => Pages\GenerateQrCode::route('/{record}/qrcode'),
        ];
    }
}
