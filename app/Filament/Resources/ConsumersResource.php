<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumersResource\Pages;
use App\Filament\Resources\ConsumersResource\RelationManagers;
use App\Models\Consumer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsumersResource extends Resource
{
    protected static ?string $model = Consumer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Consumer Management';

    protected static ?string $slug = 'Consumers';

    protected static ?string $navigationLabel = 'Consumers';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view consumers');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create consumers');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit consumers');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete consumers');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete consumers');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([Forms\Components\TextInput::make('name')->required(), 
        Forms\Components\TextInput::make('address')->required(), 
        Forms\Components\TextInput::make('nic')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('name')->sortable()->searchable(), 
            Tables\Columns\TextColumn::make('address')->sortable()->searchable(), 
            Tables\Columns\TextColumn::make('nic')->sortable()->searchable()])
            ->filters([
                //
            ])->paginated([10, 25, 50])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsumers::route('/'),
            'create' => Pages\CreateConsumer::route('/create'),
            'edit' => Pages\EditConsumer::route('/{record}/edit'),
        ];
    }
}
