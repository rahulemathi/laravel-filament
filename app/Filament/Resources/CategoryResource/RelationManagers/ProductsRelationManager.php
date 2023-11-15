<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Forms\Components\Group::make([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
               Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
               ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
