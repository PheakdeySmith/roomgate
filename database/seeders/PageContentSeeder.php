<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('page_contents')->truncate();
        DB::table('page_contents')->insert([
            [
                'id' => 1,
                'key' => 'seo',
                'title' => 'RoomGate',
                'subtitle' => null,
                'content' => 'The all-in-one platform for managing your rental properties, tenants, and finances with ease. Simplify your landlord experience.',
                'image_path' => 'uploads/hero/hero_68a9de2fe8b10.png',
                'button_text' => null,
                'button_link' => null,
                'video_url' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'key' => 'hero',
                'title' => 'Room Rental System That Powers Real Growth',
                'subtitle' => '/ Welcome to RoomGate',
                'content' => 'Connecting landlords and tenants for a seamless and transparent rental experience.',
                'image_path' => 'uploads/hero/hero_68ac765910802.avif',
                'button_text' => 'Start Free Trial',
                'button_link' => null,
                'video_url' => 'https://youtu.be/ltFNlTWDgU8?si=QhZ9t4aT0TnaFDva',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'key' => 'benefits_intro',
                'title' => 'Why Choose RoomGate',
                'subtitle' => '/ Benefits',
                'content' => null,
                'image_path' => 'uploads/hero/hero_68a9ddd25acf7.avif',
                'button_text' => null,
                'button_link' => null,
                'video_url' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'key' => 'features_intro',
                'title' => 'All Your SaaS Tools, In One Intuitive Platform',
                'subtitle' => '/ Features',
                'content' => 'From pipeline management to sales automation and reporting, Flowis brings everything your team needs into one seamless CRM experience.',
                'image_path' => null,
                'button_text' => 'See All Features',
                'button_link' => null,
                'video_url' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'key' => 'faq_intro',
                'title' => 'Everything You Need to Know Upfront',
                'subtitle' => '/ FAQ',
                'content' => 'From setup to support and pricing, here are quick answers to the most common questions we get from teams considering RoomGate',
                'image_path' => null,
                'button_text' => null,
                'button_link' => null,
                'video_url' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'key' => 'all_in_one',
                'title' => 'Power Pipeline Performance. Drive Revenue Faster',
                'subtitle' => '/ ALL-IN-ONE',
                'content' => 'Bring clarity to your entire sales process—track deals, automate follow-ups, and close with confidence in one purpose-built platform',
                'image_path' => null,
                'button_text' => null,
                'button_link' => null,
                'video_url' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'key' => 'terms-and-conditions',
                'title' => 'Terms & Conditions',
                'subtitle' => '/ Terms',
                'content' => '<h2>Use of Services</h2>
<p>You may use RoomGate only in compliance with these Terms and all applicable laws. You are responsible for any activity that occurs under your account.</p>
<h3>Account Registration</h3>
<p>You must provide accurate and complete information when creating an account. You’re responsible for maintaining the confidentiality of your login credentials.</p>
<p>RoomGate offers paid plans with monthly or annual billing. By subscribing, you authorize us to charge your payment method on a recurring basis until cancellation.</p>
<p>All payments are non-refundable except as required by law. You can cancel your plan at any time from your account settings.</p>
<h3>User Data</h3>
<p>We do not own your data. By using RoomGate, you grant us a license to store, process, and transmit data solely to provide the service.</p>
<h3>Prohibited Conduct</h3>
<p><strong>You agree not to:</strong></p>
<ul role="list">
    <li>Use the service for any unlawful or harmful purpose</li>
    <li>Use the service for any unlawful or harmful purpose</li>
    <li>Reverse engineer or copy any part of our product</li>
</ul>
<h3>Termination</h3>
<p>We reserve the right to suspend or terminate your account if you breach these Terms or misuse the service. Upon termination, your access will be revoked, and data may be deleted after 30 days.</p>
<p>We may update these Terms occasionally. If changes are material, we’ll notify you via email or platform alert. Continued use after changes means you accept the new Terms.</p>
<h3>Limitation of Liability</h3>
<p>To the maximum extent permitted by law, RoomGate and its affiliates shall not be liable for any indirect, incidental, or consequential damages arising from your use of the platform.</p>
<p>These Terms are governed by and construed under the laws of the State of California, without regard to its conflict of law principles.</p>',
                'image_path' => null,
                'button_text' => null,
                'button_link' => null,
                'video_url' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
