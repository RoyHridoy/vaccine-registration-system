<?php

namespace App\Filament\Resources;

use App\Enum\VaccineStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\VaccineCenter;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('nid')->label('NID')->icon('heroicon-o-identification'),
                TextColumn::make('email')->label('Email Address'),
                TextColumn::make('vaccineCenter.name'),
                TextColumn::make('status')
                    ->label('Vaccine Status')
                    ->formatStateUsing(fn ($state) => VaccineStatus::tryFrom($state->value)->name)
                    ->badge()
                    ->color(fn ($state): string => match ($state->name) {
                        'NOT_SCHEDULED' => 'gray',
                        'SCHEDULED' => 'warning',
                        'VACCINATED' => 'success',
                    }),
                TextColumn::make('scheduled_at')
                    ->label('Date')
                    ->date()
                    ->alignEnd(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        1 => 'Not Scheduled',
                        2 => 'Scheduled',
                        3 => 'Vaccinated',
                    ]),
                SelectFilter::make('vaccine_center_id')
                    ->multiple()
                    ->label('Vaccine Center')
                    ->options(
                        VaccineCenter::pluck('name', 'id')->toArray()
                    ),
            ])
            ->actions([

            ])
            ->bulkActions([

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
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
