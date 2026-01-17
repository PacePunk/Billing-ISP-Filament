<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Pelanggan'),

                    Forms\Components\Select::make('router_id')
                        ->relationship('router', 'name')
                        ->required()
                        ->label('Server Router'),

                    Forms\Components\Select::make('package_id')
                        ->relationship('package', 'name')
                        ->required()
                        ->reactive() // Fitur agar harga otomatis berubah
                        ->afterStateUpdated(fn ($state, callable $set) => 
                            $set('price', Package::find($state)?->price ?? 0)
                        )
                        ->label('Pilih Paket'),

                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->label('Harga Deal'),

                    Forms\Components\TextInput::make('mikrotik_username')
                        ->required()
                        ->label('Username PPPoE'),

                    Forms\Components\TextInput::make('mikrotik_password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->label('Password PPPoE'),

                    Forms\Components\TextInput::make('due_date')
                        ->numeric()
                        ->default(20)
                        ->label('Tgl Jatuh Tempo'),

                    Forms\Components\Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'isolated' => 'Isolated (Telat Bayar)',
                        ])
                        ->default('active')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')->searchable()->label('Pelanggan'),
                Tables\Columns\TextColumn::make('package.name')->label('Paket'),
                Tables\Columns\TextColumn::make('mikrotik_username')->label('User PPPoE'),
                Tables\Columns\TextColumn::make('price')->money('IDR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'isolated',
                        'danger' => 'inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}