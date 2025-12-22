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

                Forms\Components\Section::make('Pricing Settings')
                    ->schema([
                        Forms\Components\TextInput::make('hourly_rate')
                            ->label('Hourly Rate ($)')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->default(100.00)
                            ->helperText('Base hourly rate per hour'),
                        Forms\Components\TextInput::make('floor_fee')
                            ->label('Additional Fee per Floor ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->helperText('Additional charge per floor'),
                        Forms\Components\TextInput::make('transportation_fee_per_mile')
                            ->label('Transportation Fee per Mile ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(2.00)
                            ->helperText('Cost per mile for transportation'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Packing Materials Prices')
                    ->schema([
                        Forms\Components\TextInput::make('small_box_price')
                            ->label('Small Box ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(3.00),
                        Forms\Components\TextInput::make('medium_box_price')
                            ->label('Medium Box ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(5.00),
                        Forms\Components\TextInput::make('large_box_price')
                            ->label('Large Box ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(7.00),
                        Forms\Components\TextInput::make('wardrobe_box_price')
                            ->label('Wardrobe Box ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(12.00),
                        Forms\Components\TextInput::make('paper_price')
                            ->label('Paper ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(6.00),
                        Forms\Components\TextInput::make('plastic_tape_price')
                            ->label('Plastic Tape ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(4.00),
                        Forms\Components\TextInput::make('bubble_wrap_price')
                            ->label('Bubble Wrap ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(10.00),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Special Services')
                    ->schema([
                        Forms\Components\TextInput::make('piano_fee')
                            ->label('Piano Fee ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->helperText('Additional charge for moving piano'),
                        Forms\Components\TextInput::make('gun_safe_fee')
                            ->label('Gun Safe Fee ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->helperText('Additional charge for moving gun safe'),
                    ])
                    ->columns(2)
                    ->collapsible(),
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
