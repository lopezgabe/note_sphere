<?php

namespace App\Imports;

use App\Models\Note;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class NotesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $note_id = str_replace(' ', "_", $row['street_address']) . '_' . $row['city'] . $row['state'] . $row['zip_code'];
        return new Note([
            'note_id' => $note_id,
            'listing_price' => $this->parseCurrencyToCents($row['list_price']),
            'upb_initial' => $this->parseCurrencyToCents($row['unpaid_principal_balance']),
            'monthly_pi' => $this->parseCurrencyToCents($row['principal_and_interest_payment']),
            'term_months' => $this->parseNumber($row['payments_remaining']),
            'interest_rate' => $this->parsePercent($row['interest_rate']),
            'url' => $row['paperstac_listing_url'],
            'listing_type' => $row['listing_type'],
            'list_date' => $this->parseDate($row['list_date']),
            'seller' => $row['seller'],
            'negotiation_type' => $row['negotiation_type'],
            'lien_position' => $row['lien_position'],
            'performance' => $row['performance'],
            'note_type' => $row['note_type'],
            'yield' => $this->parsePercent($row['yield']),
            'interest_only_loan' => $this->parseBoolean($row['interest_only_loan']),
            'property_value' => $row['property_value'],
            'property_value_type' => $row['property_value_type'],
            'itb' => $this->parsePercent($row['itb']),
            'itv' => $this->parsePercent($row['itv']),
            'ltv' => $this->parsePercent($row['ltv']),
            'origination_date' => $this->parseDate($row['origination_date']),
            'original_balance' => $this->parseCurrencyToCents($row['original_balance']),
            'total_payoff' => $this->parseCurrencyToCents($row['total_payoff']),
            'street_address' => $row['street_address'],
            'city' => $row['city'],
            'state' => $row['state'],
            'zip_code' => $row['zip_code'],
            'property_type' => $row['property_type'],
            'last_payment_received' => $this->parseDate($row['last_payment_received']),
            'next_payment_date' => $this->parseDate($row['next_payment']),
            'maturity_date' => $this->parseDate($row['maturity_date']),
            'accrued_late_charges' => $this->parseCurrencyToCents($row['accrued_late_charges']),
            'hardest_hit_fund' => $this->parseBoolean($row['hardest_hit_fund_state']),
            'judicial_state' => $this->parseBoolean($row['judicial_state']),
            'non_judicial_state' => $this->parseBoolean($row['non_judicial_state']),
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function rules(): array
    {
        return [
            'paperstac_listing_url' => 'required',
            'listing_type' => 'required',
            'list_date' => 'required',
            'seller' => 'required'
        ];
    }

    private function parseCurrencyToCents($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        $cleaned = preg_replace('/[\$,\s]/', '', $value); // Remove $, commas, spaces
        return is_numeric($cleaned) ? (int) (floatval($cleaned) * 100) : null;
    }

    private function parsePercentageToDecimal($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        $cleaned = preg_replace('/[%,\s]/', '', $value); // Remove %, commas, spaces
        return is_numeric($cleaned) ? floatval($cleaned) / 100 : null;
    }

    /**
     * Parse date values into Carbon instances or null
     */
    private function parseDate($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->toDateString() : null; // Adjust format based on CSV
    }

    /**
     * Parse numeric values, removing commas or currency symbols if present
     */
    private function parseNumber($value, $isInteger = false)
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        $cleaned = preg_replace('/[^0-9.-]/', '', $value); // Remove non-numeric chars except . and -
        return $isInteger ? (int) $cleaned : (float) $cleaned;
    }

    /**
     * Parse boolean values from various formats
     */
    private function parseBoolean($value)
    {
        if (is_null($value) || $value === '') {
            return false; // Default to false if empty
        }
        return in_array(strtolower($value), ['yes', 'true', '1', 'y'], true);
    }

    /**
     * Parse Percent values from various formats
     */
    private function parsePercent($value)
    {
        if (is_null($value) || $value === '' || $value==='N/A') {
            return 0;
        }
        $cleaned = preg_replace('/[^0-9.-]/', '', $value); // Remove non-numeric chars except . and -
        return (float) $cleaned / 100;
    }
}
