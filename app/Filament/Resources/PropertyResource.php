<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Property Management';
    protected static ?string $navigationLabel = 'Property';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view properties');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create properties');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit properties');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete properties');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete properties');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('consumer_id')
                    ->relationship('consumer', 'name')
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\Consumer::find($value)?->name ?? '')
                    ->required(),
                TextInput::make('account_no')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('consumer.name')->label('Consumer')->sortable(),
                TextColumn::make('account_no')->sortable(),
                TextColumn::make('address')->sortable(),
            ])
            ->filters([
                // ...
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
