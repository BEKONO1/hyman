<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\TranslationTrait;

class Setting extends Model implements  HasMedia
{
    use InteractsWithMedia, HasFactory;
    use TranslationTrait;
    protected $table = "settings";
    protected $primaryKey = "id";
    protected $fillable = ['type','key','value'];
    public $timestamps = false;

    // Define default theme colors
    const DEFAULT_COLORS = [
        'primary_color' => '#5F60B9',
        'secondary_color' => '#8F9FBC'
    ];

    /**
     * Get theme settings with colors
     * @return object
     */
    public static function getThemeSettings()
    {
        return Cache::remember('theme.settings', 3600, function () {
            $settings = self::where('type', 'theme-setup')
                           ->where('key', 'theme-setup')
                           ->first();

            if ($settings && $settings->value) {
                $themeSettings = json_decode($settings->value, true);
                return (object) array_merge(self::DEFAULT_COLORS, $themeSettings);
            }

            return (object) self::DEFAULT_COLORS;
        });
    }

    /**
     * Save theme colors
     * @param array $colors
     * @return Setting
     */
    public static function saveThemeColors($colors)
    {
        // Validate and filter colors
        $validColors = array_intersect_key(
            $colors, 
            self::DEFAULT_COLORS
        );

        // Ensure valid hex colors
        foreach ($validColors as $key => $color) {
            if (!preg_match('/^#[a-f0-9]{6}$/i', $color)) {
                $validColors[$key] = self::DEFAULT_COLORS[$key];
            }
        }

        // Merge with defaults for any missing colors
        $finalColors = array_merge(self::DEFAULT_COLORS, $validColors);

        // Save to database
        $setting = self::updateOrCreate(
            [
                'type' => 'theme-setup',
                'key' => 'theme-setup'
            ],
            [
                'value' => json_encode($finalColors)
            ]
        );

        // Clear the cache
        Cache::forget('theme.settings');

        return $setting;
    }

    /**
     * Reset theme colors to defaults
     * @return Setting
     */
    public static function resetThemeColors()
    {
        return self::saveThemeColors(self::DEFAULT_COLORS);
    }

    /**
     * Reset specific theme color
     * @param string $colorKey
     * @return Setting|null
     */
    public static function resetThemeColor($colorKey)
    {
        if (!array_key_exists($colorKey, self::DEFAULT_COLORS)) {
            return null;
        }

        $currentSettings = self::getThemeSettings();
        $colors = (array) $currentSettings;
        $colors[$colorKey] = self::DEFAULT_COLORS[$colorKey];

        return self::saveThemeColors($colors);
    }

    /**
     * Validate hex color
     * @param string $color
     * @return bool
     */
    public static function isValidHexColor($color)
    {
        return preg_match('/^#[a-f0-9]{6}$/i', $color);
    }

    /**
     * Get CSS variables for theme colors
     * @return string
     */
    public static function getThemeColorsCss()
    {
        $colors = self::getThemeSettings();
        
        return <<<CSS
        :root {
            --bs-primary: {$colors->primary_color};
            --bs-secondary: {$colors->secondary_color};
            --bs-primary-rgb: #{$colors->primary_color};
            --bs-secondary-rgb: #{$colors->secondary_color};
        }
        CSS;
    }

    protected $casts = [
        'type' => 'string',
        'key'   => 'string',
        'value' => 'string',
    ];
    public function translations()
    {
        return $this->morphMany(Translations::class, 'translatable');
    }

   public function translate($attribute, $locale = null)
    {
        
        $locale = $locale ?? app()->getLocale() ?? 'en';
        if($locale !== 'en'){
            $translation = $this->translations()
            ->where('attribute', $attribute)
            ->where('locale', $locale)
            ->value('value');

        return $translation !== null ?  $translation : '';
        }
        return $this->$attribute;
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'value','id')
            ->withDefault(function () { return (object) []; });
    }
    public static function getAllSettings()
    {
        return Cache::rememberForever('settings.all', function () {
            return self::all();
        });
    }

    /**
     * Flush the cache.
     */
    public static function flushCache()
    {
        Cache::forget('settings.all');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });

        static::created(function () {
            self::flushCache();
        });

        static::deleted(function () {
            self::flushCache();
        });
    }
    public static function getValueByKey($key,$type,$langauge = null)
    {
        $setting = self::where('key', $key);
    
        if ($type !== null) {
            $setting->where('type', $type);
        }
    
        $setting = $setting->first();
    
        if ($setting) {
            // if ($key === 'privacy_policy' || $key === 'terms_condition'|| $key === 'about_us'|| $key === 'help_support' || $key === 'refund_cancellation_policy' || $key === 'data_deletion_request' || $key === 'earning-setting' || $key === 'userdashboard-setting') {
                // $decodedValue = json_decode($setting->value, true);

                // if (in_array($key, ['privacy_policy', 'terms_condition', 'about_us', 'help_support', 'refund_cancellation_policy', 'data_deletion_request'])) {
                //     if (isset($decodedValue['status']) && $decodedValue['status'] == '1') {
                //         return $decodedValue[$key] ?? '';
                //     }
                //     return null;
                // }
            if ($key === 'privacy_policy' || $key === 'terms_condition'|| $key === 'about_us'|| $key === 'help_support' || $key === 'refund_cancellation_policy' || $key === 'data_deletion_request' || $key === 'earning-setting' || $key === 'userdashboard-setting') {
                $decodedValue = json_decode($setting->value, true);
                
                if (in_array($key, ['privacy_policy', 'terms_condition', 'about_us', 'help_support', 'refund_cancellation_policy', 'data_deletion_request'])) {
                    
                    if (isset($decodedValue['status']) && $decodedValue['status'] == '1') {
                        
                        $setting =  $setting->getTranslation($setting->translations, $langauge, 'value', $decodedValue[$key]) ?? $decodedValue[$key];
                        return $setting ?? '';
                    }
                    return null;
                }
                return $setting->value;
                // $setting = $setting->getTranslation($setting->translations, $langauge, 'value', $setting->value) ?? $setting->value;
                // return $setting;
            } else {
                return json_decode($setting->value);
            }
        }

    return null;
    
    }
}
