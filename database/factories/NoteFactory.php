<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(), // Generates a fake
            'listing_type' => 'Single Asset',
            'list_date' => $this->faker->date('Y-m-d'),
            'seller' => $this->faker->name(),
            'listing_price' => $this->faker->randomFloat(2),
            'negotiation_type' => 'Firm',
            'lien_position' => 1,
            'performance' => 'Performing',
            'note_type' => 'Mortgage',
            'interest_rate' => $this->faker->randomFloat(2),
            'yield' => $this->faker->randomFloat(2),
            'interest_only' => $this->faker->boolean(),
            'property_value' => $this->faker->randomNumber(2),
            'property_value_type' => 'Appraisal',
            'itb' => $this->faker->randomNumber(2),
            'itv' => $this->faker->randomNumber(2),
            'ltv' => $this->faker->randomNumber(2),
            'origination_date' => $this->faker->date('Y-m-d'),
            'original_balance' => $this->faker->randomNumber(2),
            'total_payoff' => $this->faker->randomNumber(2),
            'payments_remaining' => $this->faker->randomNumber(0),
            'street_address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->streetAddress(),
            'zip_code' => $this->faker->postcode(),
            'property_type' => 'Single Family',
            'pi_payment' => $this->faker->randomNumber(2),
            'last_payment_received' => $this->faker->date('Y-m-d'),
            'next_payment_date' => $this->faker->date('Y-m-d'),
            'maturity_date' => $this->faker->date('Y-m-d'),
            'accrued_late_charges' => $this->faker->randomNumber(2),
            'hardest_hit_fund' => $this->faker->boolean(),
            'judicial_state' => $this->faker->boolean(),
            'non_judicial_state' => $this->faker->boolean(),
        ];
    }
}
