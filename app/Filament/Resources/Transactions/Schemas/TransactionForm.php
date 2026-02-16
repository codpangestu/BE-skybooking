<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Detail')
                    ->schema([
                        Placeholder::make('code')
                            ->content(fn($record) => $record?->code),
                        Placeholder::make('name')
                            ->content(fn($record) => $record?->name),
                        Placeholder::make('email')
                            ->content(fn($record) => $record?->email),
                        Placeholder::make('phone')
                            ->content(fn($record) => $record?->phone),
                    ])->columns(2),

                Section::make('Flight Detail')
                    ->schema([
                        Placeholder::make('flight')
                            ->content(fn($record) => $record?->flight?->flight_number),
                        Placeholder::make('class')
                            ->content(fn($record) => $record?->flightClass?->class_type),
                        Placeholder::make('passengers')
                            ->content(fn($record) => $record?->number_of_passengers),
                    ])->columns(3),

                Section::make('Payment Information')
                    ->schema([
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'success' => 'Success',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                        Placeholder::make('subtotal')
                            ->content(fn($record) => 'IDR ' . number_format($record?->subtotal)),
                        Placeholder::make('promo')
                            ->content(fn($record) => $record?->promoCode?->code ?? '-'),
                        Placeholder::make('grandtotal')
                            ->content(fn($record) => 'IDR ' . number_format($record?->grandtotal)),
                    ])->columns(2),
            ]);
    }
}
