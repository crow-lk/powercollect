<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerUsageResource\Pages;
use App\Filament\Resources\CustomerUsageResource\RelationManagers;
use App\Models\CustomerUsage;
use App\Models\Customer;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerUsageResource extends Resource
{
    protected static ?string $model = CustomerUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Usage';

    protected static ?string $navigationLabel = 'Customer Usage';

    protected static ?string $pluralModelLabel = 'Customer Usage Records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Usage Information')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->options(Customer::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->preload(),
                        Forms\Components\Select::make('house_part_id')
                            ->label('House Part')
                            ->relationship('housePart', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('equipment_id')
                            ->label('Equipment')
                            ->options(Equipment::all()->pluck('type', 'id'))
                            ->searchable()
                            ->required()
                            ->preload(),
                        Forms\Components\TextInput::make('kVA')
                            ->label('kVA')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0),
                        Forms\Components\DatePicker::make('date')
                            ->label('Usage Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Start Time')
                            ->required()
                            ->seconds(false),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('End Time')
                            ->required()
                            ->seconds(false)
                            ->after('start_time'),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.account_no')
                    ->label('Account No')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('housePart.house.account_no')
                    ->label('Account No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('housePart.name')
                    ->label('House Part')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('equipment.type')
                    ->label('Equipment')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kVA')
                    ->label('kVA')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('H:i')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(Customer::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('equipment_id')
                    ->label('Equipment')
                    ->options(Equipment::all()->pluck('type', 'id'))
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
            'index' => Pages\ListCustomerUsages::route('/'),
            'create' => Pages\CreateCustomerUsage::route('/create'),
            'edit' => Pages\EditCustomerUsage::route('/{record}/edit'),
        ];
    }
}
