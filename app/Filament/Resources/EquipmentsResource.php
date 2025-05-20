<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentsResource\Pages;
use App\Filament\Resources\EquipmentsResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipmentsResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')->required(),
Forms\Components\TextInput::make('brand')->required(),
Forms\Components\TextInput::make('model'),
Forms\Components\TextInput::make('kVA')->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->sortable()->searchable(),
Tables\Columns\TextColumn::make('brand')->sortable()->searchable(),
Tables\Columns\TextColumn::make('model')->sortable()->searchable(),
Tables\Columns\TextColumn::make('kVA')->sortable()->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEquipments::route('/'),
        ];
    }
}
