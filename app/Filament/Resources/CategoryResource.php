<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make([
                        Forms\Components\TextInput::make('name')->required()->live(onBlur:true)->unique()->afterStateUpdated(function(string $operation,$state,Forms\Set $set){
                            if($operation != 'create'){
                                return;
                            }
                            $set('slug' ,Str::slug($state));
                        }),
                        Forms\Components\TextInput::make('slug')->disabled()->dehydrated()->required()->unique(Product::class,'slug',ignoreRecord:true),
                        Forms\Components\MarkdownEditor::make('description')->columnSpanFull(),

                    ])->columns(2)
                    ]),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Status')->schema([
                            Forms\Components\Toggle::make('is_visible')->label('Visibility')->helperText('Enable or Disable visibility')->default(true),
                            Forms\Components\Select::make('parent_id')->relationship('parent','name')
                        ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('parent.name')->label('Parnet')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('visible')->label('Visibility')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated Date')->sortable()->date()
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }    
}
