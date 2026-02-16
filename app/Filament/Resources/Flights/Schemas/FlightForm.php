<?php

namespace App\Filament\Resources\Flights\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FlightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Main Information')
                    ->schema([
                        TextInput::make('flight_number')
                            ->required()
                            ->maxLength(255),
                        Select::make('airline_id')
                            ->relationship('airline', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Section::make('Flight Segments')
                    ->description('Add departure, transit (if any), and arrival details.')
                    ->schema([
                        Repeater::make('segments')
                            ->relationship()
                            ->schema([
                                Select::make('airport_id')
                                    ->relationship('airport', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('sequence')
                                    ->numeric()
                                    ->required()
                                    ->default(1),
                                DateTimePicker::make('time')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->itemLabel(fn(array $state): ?string => 'Segment ' . ($state['sequence'] ?? '')),
                    ]),

                Section::make('Flight Classes')
                    ->description('Define prices and capacity for each class.')
                    ->schema([
                        Repeater::make('classes')
                            ->relationship()
                            ->schema([
                                Select::make('class_type')
                                    ->options([
                                        'economy' => 'Economy',
                                        'business' => 'Business',
                                        'first' => 'First Class',
                                    ])
                                    ->required(),
                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->required(),
                                TextInput::make('total_seats')
                                    ->numeric()
                                    ->required(),
                                Select::make('facilities')
                                    ->relationship('facilities', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
