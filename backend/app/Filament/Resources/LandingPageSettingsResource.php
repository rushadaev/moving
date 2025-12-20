<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageSettingsResource\Pages;
use App\Filament\Resources\LandingPageSettingsResource\RelationManagers;
use App\Models\LandingPageSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandingPageSettingsResource extends Resource
{
    protected static ?string $model = LandingPageSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Landing Page';

    protected static ?string $navigationGroup = 'Landing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Header Section')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->disk('public')
                            ->directory('landing/logos')
                            ->visibility('public')
                            ->helperText('Upload logo image'),
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->default('MOOWEE'),
                        Forms\Components\TextInput::make('tagline')
                            ->maxLength(255)
                            ->helperText('Main tagline displayed below company name'),
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Company description'),
                    ]),

                Forms\Components\Section::make('Photo Section')
                    ->schema([
                        Forms\Components\TextInput::make('photo_title')
                            ->required()
                            ->maxLength(255)
                            ->default('Unloaded photo'),
                        Forms\Components\FileUpload::make('photo_url')
                            ->image()
                            ->disk('public')
                            ->directory('landing/photos')
                            ->visibility('public')
                            ->helperText('Upload photo for photo section'),
                    ]),

                Forms\Components\Section::make('Video Section')
                    ->schema([
                        Forms\Components\TextInput::make('video_title')
                            ->required()
                            ->maxLength(255)
                            ->default('Unloaded video'),
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL')
                            ->maxLength(255)
                            ->helperText('Enter YouTube video URL or direct video link (e.g., https://www.youtube.com/embed/VIDEO_ID or https://example.com/video.mp4)')
                            ->placeholder('https://www.youtube.com/embed/VIDEO_ID')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('instagram_url')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('facebook_url')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('youtube_url')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListLandingPageSettings::route('/'),
            'create' => Pages\CreateLandingPageSettings::route('/create'),
            'edit' => Pages\EditLandingPageSettings::route('/{record}/edit'),
        ];
    }
}
