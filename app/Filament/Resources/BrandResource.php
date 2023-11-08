<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BrandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BrandResource\RelationManagers;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\COmponents\Group::make([
                    Forms\Components\TextInput::make('name')->required()->required()->live(onBlur:true)->unique()->afterStateUpdated(function(string $operation,$state,Forms\Set $set){
                        if($operation != 'create'){
                            return;
                        }
                        $set('slug' ,Str::slug($state));
                    }),
                    Forms\Components\TextInput::make('slug')->disabled()->dehydrated()->required()->unique(),
                    Forms\Components\TextInput::make('url')->label('website url')->required()->unique()->columnSpan('full'),
                    Forms\Components\MarkdownEditor::make('descriptions')->columnSpan('full')
                ])->columns(2),
                Forms\Components\Group::make()->schema([
                    Section::make('Status')->schema([
                        Forms\Components\Toggle::make('is_visible')->label('Visibility')->helperText('Enable or Disable Brand Visibility')->default(true),
                    ]),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Color')->schema([
                            Forms\Components\ColorPicker::make('primary_hex')->label('Primary Color')
                        ])
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')->label('Website Url')->sortable()->searchable(),
                Tables\Columns\ColorColumn::make('primary_hex')->label('Primary Color'),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->sortable()->label('Visibility'),
                Tables\Columns\TextColumn::make('updated_at')->date()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }    
}
