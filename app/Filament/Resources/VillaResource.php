<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillaResource\Pages;
use App\Filament\Resources\VillaResource\RelationManagers;
use App\Models\Villa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\FileUpload;

class VillaResource extends Resource
{
    protected static ?string $model = Villa::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price_per_night')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('villas')
                    ->maxSize(2048)
                    ->helperText('Max 2MB. Uploaded image will be stored as-is (no resizing or compression).')
                    ->validationMessages([
                        'max' => 'The image must be 2MB or less. Please choose a smaller file.',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('max_guests')
                    ->required()
                    ->numeric()
                    ->default(2),
                Forms\Components\TextInput::make('bedrooms')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('bathrooms')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('area_sqm')
                    ->numeric()
                    ->suffix('m²'),
                Forms\Components\TagsInput::make('amenities')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_featured')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_night')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('max_guests')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bedrooms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bathrooms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('area_sqm')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListVillas::route('/'),
            'create' => Pages\CreateVilla::route('/create'),
            'edit' => Pages\EditVilla::route('/{record}/edit'),
        ];
    }
}
