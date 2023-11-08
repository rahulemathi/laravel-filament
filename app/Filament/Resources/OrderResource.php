<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order Details')->schema([
                        Forms\Components\TextInput::make('number')->default('OR-'. random_int(100000,999999))->disabled()->dehydrated()->required(),
                        Forms\Components\Select::make('customer_id')->relationship('customer','name')->searchable()->required()->required(),
                        Forms\Components\Select::make('type')->options([
                            'pending'=>'pending',
                            'processing'=>'processing',
                            'completed'=>'completed',
                            'declined'=>'declined'
                        ])->columnSpanFull()->required(),
                        Forms\Components\MarkdownEditor::make('notes')->columnSpanFull()
                    ])->columns(2),
                    Forms\Components\Wizard\Step::make('Order Items')->schema([
                       Forms\Components\Repeater::make('items')->relationship()->schema([
                        Forms\Components\Select::make('product_id')->label('Product')->options(Product::query()->pluck('name','id')),
                        Forms\Components\TextInput::make('quantity')->numeric()->default(1)->required(),
                        Forms\Components\TextInput::make('unit_price')->label('Unit Price')->disabled()->dehydrated()->numeric()->required()
                       ])->columns(3)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                Tables\Columns\TextColumn::make('number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('total_price')->searchable()->sortable()->summarize([
                    Tables\Columns\Summarizers\Sum::make()->money()
                ]),
                Tables\Columns\TextColumn::make('created_at')->label('Ordered Date')->date()
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }    
}
