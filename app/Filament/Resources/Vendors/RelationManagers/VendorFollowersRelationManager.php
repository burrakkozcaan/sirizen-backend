<?php

namespace App\Filament\Resources\Vendors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorFollowersRelationManager extends RelationManager
{
    protected static string $relationship = 'followers';

    protected static ?string $title = 'Takipçiler';

    protected static ?string $modelLabel = 'Takipçi';

    protected static ?string $pluralModelLabel = 'Takipçiler';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Kullanıcı')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('vendor_followers.created_at')
                    ->label('Takip Tarihi')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Takipçi Ekle')
                    ->preloadRecordSelect()
                    ->modalHeading('Takipçi Ekle')
                    ->modalDescription('Bu satıcıya takipçi ekleyin'),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label('Kaldır')
                    ->modalHeading('Takipçiyi Kaldır')
                    ->modalDescription('Bu kullanıcının takibini kaldırmak istediğinizden emin misiniz?'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilenleri Kaldır'),
                ]),
            ])
            ->defaultSort('vendor_followers.created_at', 'desc')
            ->emptyStateHeading('Henüz takipçi yok')
            ->emptyStateDescription('Bu satıcıyı henüz kimse takip etmiyor.');
    }
}
