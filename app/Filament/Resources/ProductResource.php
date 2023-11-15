<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnums;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Product';
    
    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 20;

    // protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge'; used to change the icon when its active

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    } // getNavigationBadge


    public static function getGloballySearchableAttributes(): array{
        return ['name','slug','description',];
    }

    public static function getGlobalSearchResultDetails(Model $record): array{
        return [
            'Brand' => $record->brand->name,
        ];
    }


    public static function getGlobalSearchableEloquentQuery():Builder{
        return parent::getGlobalSearchableEloquentQuery()->with(['brand']);
    }


    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('name')->required()->live(onBlur:true)->unique()->afterStateUpdated(function(string $operation,$state,Forms\Set $set){
                            if($operation != 'create'){
                                return;
                            }
                            $set('slug' ,Str::slug($state));
                        }),
                        Forms\Components\TextInput::make('slug')->disabled()->dehydrated()->required()->unique(Product::class,'slug',ignoreRecord:true),
                        Forms\Components\MarkdownEditor::make('description')->columnSpan('full')
                    ])->columns(2),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('Pricing & Inventory')->schema([
                         Forms\Components\TextInput::make('sku')->label("SKU (Stock Keeping Unit)")->unique()->required(),
                         Forms\Components\TextInput::make('price')->numeric()->rules('regex:/^\d{1,6}(\.\d{0,2})?$/')->required(),
                         Forms\Components\TextInput::make('quantity')->numeric()->minValue(0)->maxValue(100)->required(),
                         Forms\Components\Select::make('type')->options([
                            'downloadable'=>'downloadable',
                            'deliverable'=>'deliverable'
                         ])->required()
                        ])->columns(2)
                        ])
                    ]),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make('status')->schema([
                           Forms\Components\Toggle::make('is_visible')->label('Visiblity')->helperText('Enable or Disable Product Visibility')->default(true),
                           Forms\Components\Toggle::make('is_featured')->label('Featured')->helperText('Enable ot Disable products featured status'),
                           Forms\Components\DatePicker::make('published_at')->label('Avaliablity')->default(now())
                        ])->collapsible(),
                        
                        Forms\Components\Section::make('Image')->schema([
                            Forms\Components\FileUpload::make('image')->directory('form-attachements')->preserveFilenames()->image()->imageEditor()
                        ]),

                        Forms\Components\Section::make('Associations ')->schema([
                           Forms\Components\Select::make('brand_id')->relationship('brand','name')->required(),
                           Forms\Components\Select::make('categories')->relationship('categories','name')->multiple()->required()
                        ]),


                    ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('brand.name')->searchable()->sortable()->toggleable(),
                Tables\Columns\IconColumn::make('is_visible')->searchable()->sortable()->toggleable()->boolean(),
                Tables\Columns\TextColumn::make('price')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('quantity')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('published_at')->date()->sortable(),
                Tables\Columns\TextColumn::make('type'),
                
                
            ])
            ->filters([
                //
                Tables\Filters\TernaryFilter::make("is_visible")->label('Visibility')->boolean()->trueLabel('Only Visible Products')->falseLabel('Only Hidden Products')->native(false),
                Tables\Filters\SelectFilter::make('brand')->relationship('brand','name')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }    
}
