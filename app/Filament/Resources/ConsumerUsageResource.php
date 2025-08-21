<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumerUsageResource\Pages;
use App\Filament\Resources\ConsumerUsageResource\RelationManagers;
use App\Models\ConsumerUsage;
use App\Models\Consumer;
use App\Models\Equipment;
use App\Models\Property;
use App\Models\PropertyPart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Card;

class ConsumerUsageResource extends Resource
{
    protected static ?string $model = ConsumerUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Usage';

    protected static ?string $navigationLabel = 'Consumer Usage';

    protected static ?string $pluralModelLabel = 'Consumer Usage Records';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view consumer usages');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create consumer usages');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit consumer usages');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete consumer usages');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete consumer usages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Usage Information')
                    ->schema([
                        Select::make('property_id')
                            ->label('Property')
                            ->options(Property::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->default(now()),
                        Repeater::make('usage_data')
                            ->label('Usage Details')
                            ->schema([
                                Select::make('property_part_id')
                                    ->label('Property Part')
                                    ->options(PropertyPart::all()
                                        ->mapWithKeys(fn ($part) => [$part->id => $part->name ?? '']))
                                    ->required()
                                    ->searchable()
                                    ->reactive(),
                                Select::make('equipment_id')
                                    ->label('Equipment')
                                    ->options(Equipment::all()->pluck('type', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->createOptionForm([
                                        TextInput::make('type')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('model')
                                            ->maxLength(255),
                                        TextInput::make('serial_number')
                                            ->maxLength(255),
                                        DatePicker::make('installation_date'),
                                        DatePicker::make('last_maintenance_date'),
                                        Select::make('status')
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'maintenance' => 'Under Maintenance',
                                            ])
                                            ->required(),
                                        Select::make('property_part_id')
                                            ->label('Property Part')
                                            ->options(PropertyPart::all()->pluck('name', 'id'))
                                            ->default(fn (callable $get) => $get('property_part_id'))
                                            ->disabled()
                                            ->dehydrated(true)
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data, callable $get) {
                                        $propertyPartId = $get('property_part_id');
                                        $data['property_part_id'] = $propertyPartId;
                                        return Equipment::create($data)->getKey();
                                    }),
                                TextInput::make('kVA')
                                    ->label('kVA (Usage Value)')
                                    ->numeric()
                                    ->required(),
                                Select::make('period_number')
                                    ->label('Time Period (15-min interval)')
                                    ->options(array_combine(range(1, 96), range(1, 96)))
                                    ->required(),
                            ])
                            ->defaultItems(1)
                            ->columns(3)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_data')
                    ->label('Usage Data')
                    ->formatStateUsing(fn (array $state): string => json_encode($state, JSON_PRETTY_PRINT))
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->options(Property::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from'),
                        Forms\Components\DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListConsumerUsages::route('/'),
            'create' => Pages\CreateConsumerUsage::route('/create'),
            'edit' => Pages\EditConsumerUsage::route('/{record}/edit'),
        ];
    }
}