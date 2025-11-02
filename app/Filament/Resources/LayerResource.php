<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayerResource\Pages;
use App\Models\Layer;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LayerResource extends Resource
{
    protected static ?string $model = Layer::class;

    protected static ?string $navigationLabel = 'Camadas Geográficas';

    protected static ?string $modelLabel = 'Camada Geográfica';

    protected static ?string $pluralModelLabel = 'Camadas Geográficas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),

                FileUpload::make('geojson_file')
                    ->label('Arquivo GeoJSON')
                    ->acceptedFileTypes(['application/json', 'application/geo+json'])
                    ->maxSize(10240) // 10MB
                    ->required()
                    ->disk('public')
                    ->directory('geojson')
                    ->helperText('Envie um arquivo GeoJSON válido contendo a geometria')
                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                        if ($state) {
                            try {
                                $content = file_get_contents(storage_path('app/public/' . $state));
                                $geojson = json_decode($content, true);

                                if (!$geojson || !isset($geojson['type'])) {
                                    throw new InvalidArgumentException('Arquivo GeoJSON inválido');
                                }

                                // Validate it's a valid geometry
                                $isValid = DB::selectOne(
                                    "SELECT ST_IsValid(ST_GeomFromGeoJSON(?)) as is_valid",
                                    [json_encode($geojson)]
                                );

                                if (!$isValid || !$isValid->is_valid) {
                                    throw new InvalidArgumentException('Geometria GeoJSON inválida');
                                }

                                // Store the geometry data for later use
                                $set('geometry_data', json_encode($geojson));
                            } catch (\Exception $e) {
                                throw new InvalidArgumentException('Erro ao processar GeoJSON: ' . $e->getMessage());
                            }
                        }
                    })
                    ->columnSpanFull(),

                Hidden::make('geometry_data')
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListLayers::route('/'),
            'create' => Pages\CreateLayer::route('/create'),
            'edit' => Pages\EditLayer::route('/{record}/edit'),
        ];
    }
}
