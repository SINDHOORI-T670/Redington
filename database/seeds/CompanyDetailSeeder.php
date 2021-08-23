<?php

use Illuminate\Database\Seeder;

class CompanyDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            [
                'key' => 'site_title',
                'value' => 'Redington'
            ],
            [
                'key' => 'site_logo',
                'value' => asset('public/admin/app-assets/images/ico/apple-icon-120.png'),
            ],
            [
                'key' => 'site_favicon',
                'value' => asset('public/admin/app-assets/images/ico/apple-icon-120.png'),
            ],
            [
                'key' => 'site_copyright',
                'value' => '&copy; '.date('Y').' CodeaTech'
            ],
            [
                'key' => 'SOCIAL_FACEBOOK_LINK',
                'value' => 'http://facebook.com'
            ],
            [
                'key' => 'SOCIAL_TWITTER_LINK',
                'value' => 'http://twitter.com'
            ],
            [
                'key' => 'SOCIAL_G-PLUS_LINK',
                'value' => 'http://google.com'
            ],
            [
                'key' => 'SOCIAL_INSTAGRAM_LINK',
                'value' => 'http://instagram.com'
            ],
            [
                'key' => 'SOCIAL_PINTEREST_LINK',
                'value' => 'http://pinterest.com'
            ],
            [
                'key' => 'SOCIAL_VIMEO_LINK',
                'value' => 'http://vimeo.com'
            ],
            [
                'key' => 'SOCIAL_YOUTUBE_LINK',
                'value' => 'http://youtube.com'
            ],
            [
                'key' => 'default_lang',
                'value' => 'en'
            ],


            

        ]);
    }
}
