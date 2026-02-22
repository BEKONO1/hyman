<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromotionalBanner;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class PromotionalBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Summer Sale Banner',
                'description' => 'Get up to 50% off on all services!',
                'status' => 'accepted', 
                'banner_type' => 'service',
                'service_id' => 15,
                'provider_id' => 4,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'duration' => 30,
                'charges' => 100,
                'total_amount' => 100,
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'is_requested_banner' => 0,
                'banner_image' => public_path('/images/slider/1.png'), 
            ],
            [
                'title' => 'Summer Sale Banner',
                'description' => 'Get up to 50% off on all services!',
                'status' => 'accepted', 
                'banner_type' => 'service',
                'service_id' => 17,
                'provider_id' => 4,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'duration' => 30,
                'charges' => 100,
                'total_amount' => 100,
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'is_requested_banner' => 0,
                'banner_image' => public_path('/images/slider/2.png'), 
            ],[
                'title' => 'Summer Sale Banner',
                'description' => 'Get up to 50% off on all services!',
                'status' => 'accepted', 
                'banner_type' => 'service',
                'service_id' => 18,
                'provider_id' => 4,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'duration' => 30,
                'charges' => 100,
                'total_amount' => 100,
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'is_requested_banner' => 0,
                'banner_image' => public_path('/images/slider/3.jpg'), 
            ],[
                'title' => 'Summer Sale Banner',
                'description' => 'Get up to 50% off on all services!',
                'status' => 'accepted', 
                'banner_type' => 'service',
                'service_id' => 20,
                'provider_id' => 4,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'duration' => 30,
                'charges' => 100,
                'total_amount' => 100,
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'is_requested_banner' => 0,
                'banner_image' => public_path('/images/slider/4.png'), 
            ],
        ];

        foreach ($banners as $val) {
            $bannerImage = $val['banner_image'] ?? null;
            $bannerData = Arr::except($val, ['banner_image']); 
            
            $banner = PromotionalBanner::create($bannerData);

            // Store banner image using Media Library
            if (isset($bannerImage) && File::exists($bannerImage)) {
                $file = new \Illuminate\Http\File($bannerImage);
                $banner->addMedia($file)
                    ->preservingOriginal()
                    ->toMediaCollection('banner_attachment'); 
            }
        }
    }
}
