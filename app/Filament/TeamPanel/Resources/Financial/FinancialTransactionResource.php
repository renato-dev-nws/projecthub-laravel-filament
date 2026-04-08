<?php

namespace App\Filament\TeamPanel\Resources\Financial;

use App\Filament\TeamPanel\Resources\Financial\FinancialTransaction\Pages\CreateFinancialTransaction;
use App\Filament\TeamPanel\Resources\Financial\FinancialTransaction\Pages\EditFinancialTransaction;
use App\Filament\TeamPanel\Resources\Financial\FinancialTransaction\Pages\ListFinancialTransactions;
use App\Models\FinancialTransaction;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class FinancialTransactionResource extends Resource
{
    protected static ?string $model = FinancialTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Transações';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Transação';

    protected static ?string $pluralModelLabel = 'Transações';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Dados da Transação')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'income'  => 'Receita',
                            'expense' => 'Despesa',
                        ])
                        ->required()
                        ->live(),

                    TextInput::make('description')
                        ->label('Descrição')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('amount')
                        ->label('Valor')
                        ->numeric()
                        ->prefix('R$')
                        ->required(),

                    DatePicker::make('due_date')
                        ->label('Vencimento')
                        ->required(),

                    DatePicker::make('paid_date')
                        ->label('Data de Pagamento')
                        ->nullable(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'   => 'Pendente',
                            'paid'      => 'Pago',
                            'overdue'   => 'Vencido',
                            'cancelled' => 'Cancelado',
                        ])
                        ->default('pending'),

                    Select::make('payment_method')
                        ->label('Forma de Pagamento')
                        ->options([
                            'credit_card'  => 'Cartão de Crédito',
                            'debit_card'   => 'Cartão de Débito',
                            'pix'          => 'Pix',
                            'boleto'       => 'Boleto Bancário',
                            'payment_link' => 'Link de Cobrança',
                            'deposit'      => 'Depósito',
                            'cash'         => 'Dinheiro',
                        ])
                        ->nullable(),
                ]),

            Section::make('Detalhes')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('project_id')
                        ->label('Projeto')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->nullable()
                        ->live()
                        ->hidden(fn (Get $get) => $get('type') !== 'income')
                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state) {
                                $project = \App\Models\Project::find($state);
                                $set('client_id', $project?->client_id);
                            } else {
                                $set('client_id', null);
                            }
                        }),

                    Select::make('client_id')
                        ->label('Cliente')
                        ->relationship('client', 'company_name')
                        ->searchable()
                        ->nullable()
                        ->hidden(fn (Get $get) => $get('type') !== 'income')
                        ->disabled(fn (Get $get) => (bool) $get('project_id'))
                        ->dehydrated(),

                    Select::make('supplier_id')
                        ->label('Fornecedor')
                        ->relationship('supplier', 'name')
                        ->searchable()
                        ->nullable(),

                    Select::make('bank_id')
                        ->label('Banco')
                        ->relationship('bank', 'name')
                        ->searchable()
                        ->required(),

                    Select::make('financial_category_id')
                        ->label('Categoria')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->nullable(),

                    TextInput::make('reference_number')
                        ->label('Número de Referência')
                        ->maxLength(100),

                    TextInput::make('payment_link')
                        ->label('Link de Pagamento')
                        ->url()
                        ->maxLength(500)
                        ->nullable(),

                    Textarea::make('notes')
                        ->label('Observações')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income'  => 'success',
                        'expense' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'income'  => 'Receita',
                        'expense' => 'Despesa',
                        default   => $state,
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'      => 'success',
                        'pending'   => 'warning',
                        'overdue'   => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid'      => 'Pago',
                        'pending'   => 'Pendente',
                        'overdue'   => 'Vencido',
                        'cancelled' => 'Cancelado',
                        default     => $state,
                    }),

                TextColumn::make('amount')
                    ->label('Valor')
                    ->prefix('R$ ')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('paid_date')
                    ->label('Pago em')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('bank.name')
                    ->label('Banco')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('payment_method')
                    ->label('Pagamento')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'credit_card'  => 'Cartão Crédito',
                        'debit_card'   => 'Cartão Débito',
                        'pix'          => 'Pix',
                        'boleto'       => 'Boleto',
                        'payment_link' => 'Link de Cobrança',
                        'deposit'      => 'Depósito',
                        'cash'         => 'Dinheiro',
                        default        => $state ?? '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'income'  => 'Receita',
                        'expense' => 'Despesa',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Pendente',
                        'paid'      => 'Pago',
                        'overdue'   => 'Vencido',
                        'cancelled' => 'Cancelado',
                    ]),

                SelectFilter::make('bank_id')
                    ->label('Banco')
                    ->relationship('bank', 'name'),
            ])
            ->recordActions([
                Action::make('mark_paid')
                    ->label('Marcar Pago')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'paid')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'paid', 'paid_date' => now()])),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFinancialTransactions::route('/'),
            'create' => CreateFinancialTransaction::route('/create'),
            'edit'   => EditFinancialTransaction::route('/{record}/edit'),
        ];
    }
}
