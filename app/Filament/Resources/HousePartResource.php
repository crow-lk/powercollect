<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HousePartResource\Pages;
use App\Models\HousePart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class HousePartResource extends Resource
{
    protected static ?string $model = HousePart::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Property Sections';

    public static function form(Form $form): Form
    {
        return $form->schema([TextInput::make('name')->required()->maxLength(255)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('id')->sortable(), TextColumn::make('name')->sortable()])->filters([
            // ...
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHouseParts::route('/'),
            'create' => Pages\CreateHousePart::route('/create'),
            'edit' => Pages\EditHousePart::route('/{record}/edit'),
        ];
    }
}
