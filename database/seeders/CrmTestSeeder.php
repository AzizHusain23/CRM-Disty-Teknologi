<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Institution;
use App\Models\Training;
use App\Models\TrainingCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CrmTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            [
                'email' => 'admin@distyakademi.com',
            ],
            [
                'name' => 'CRM Admin',
                'password' => 'password',
            ]
        );

        $institution = Institution::create([
            'name' => 'PT Demo Akademi',
            'type' => 'company',
            'email' => 'info@demo-akademi.com',
            'phone' => '081234567890',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
        ]);

        $customer = Customer::create([
            'customer_code' => 'CUS-000001',
            'institution_id' => $institution->id,
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'status' => 'active',
            'source' => 'manual',
        ]);

        $category = TrainingCategory::create([
            'name' => 'Data & Analytics',
            'description' => 'Training data analytics.',
        ]);

        $training = Training::create([
            'training_category_id' => $category->id,
            'name' => 'Microsoft Power BI',
            'description' => 'Training Power BI dasar sampai lanjutan.',
            'price' => 1500000,
            'duration_hours' => 16,
        ]);

        $customer->registrations()->create([
            'training_id' => $training->id,
            'training_date' => now()->toDateString(),
            'status' => 'completed',
            'amount' => 1500000,
            'registration_number' => 'REG-000001',
        ]);

        $customer->activities()->create([
            'user_id' => $user->id,
            'type' => 'whatsapp',
            'subject' => 'Follow up training',
            'description' => 'Customer tertarik mengikuti training Power BI.',
            'activity_at' => now(),
        ]);

        $customer->followUps()->create([
            'assigned_to' => $user->id,
            'title' => 'Follow up Power BI Advanced',
            'description' => 'Hubungi customer untuk menawarkan kelas lanjutan.',
            'follow_up_at' => now()->addDays(3),
            'priority' => 'normal',
            'status' => 'pending',
        ]);
    }
}