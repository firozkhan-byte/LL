<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CRMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = DB::table('customers')->get();
        $user = DB::table('users')->first();

        foreach ($customers as $idx => $c) {
            // 1. Seed Customer Wallet
            $walletId = Str::uuid()->toString();
            $balance = ($idx === 0) ? 1500.00 : 800.00;
            DB::table('customer_wallets')->insert([
                'id' => $walletId,
                'customer_id' => $c->id,
                'balance' => $balance,
                'currency' => 'INR',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Wallet Transaction
            DB::table('customer_wallet_transactions')->insert([
                'id' => Str::uuid()->toString(),
                'customer_wallet_id' => $walletId,
                'transaction_type' => 'deposit',
                'amount' => $balance,
                'reference_type' => 'opening',
                'reference_id' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            // 2. Seed Customer Profile
            DB::table('customer_profiles')->insert([
                'id' => Str::uuid()->toString(),
                'customer_id' => $c->id,
                'birthday' => now()->subYears(28)->format('Y-m-d'),
                'anniversary' => now()->subYears(2)->format('Y-m-d'),
                'preferences' => json_encode([
                    'preferred_category' => 'Spirit',
                    'preferred_brand' => 'Johnnie Walker',
                ]),
                'notes' => 'Prefers premium liquor brands. Highly active customer.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Seed Support Ticket
            DB::table('crm_tickets')->insert([
                'id' => Str::uuid()->toString(),
                'customer_id' => $c->id,
                'type' => ($idx === 0) ? 'complaint' : 'support',
                'subject' => ($idx === 0) ? 'Delivery Delayed' : 'Loyalty Point Query',
                'description' => ($idx === 0)
                    ? 'My online order SO-2026-0002 has not been shipped yet.'
                    : 'I want to confirm how many loyalty points I earn per ₹100 spent.',
                'status' => ($idx === 0) ? 'in_progress' : 'resolved',
                'priority' => ($idx === 0) ? 'high' : 'low',
                'assigned_to' => $user?->id,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ]);
        }

        // 4. Seed Marketing Campaign
        DB::table('crm_campaigns')->insert([
            'id' => Str::uuid()->toString(),
            'name' => 'Monsoon Single Malt Festival Discount',
            'channel' => 'sms',
            'subject' => null,
            'content' => 'Living Liquidz: Get 10% off on all Single Malt bottles using code SINGLEMALT10 today!',
            'status' => 'sent',
            'sent_count' => 150,
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);
    }
}
