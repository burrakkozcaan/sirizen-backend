<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorReturnPoliciesRelationManager extends RelationManager
{
    protected static string $relationship = 'returnPolicies';

    protected static ?string $title = 'İade Politikaları';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('days')
                    ->label('İade Süresi (gün)')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->default(15),

                Toggle::make('is_free')
                    ->label('Ücretsiz İade')
                    ->required(),

                Textarea::make('conditions')
                    ->label('Koşullar')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('days')
                    ->label('Gün')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_free')
                    ->label('Ücretsiz')
                    ->boolean(),

                TextColumn::make('conditions')
                    ->label('Koşullar')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
