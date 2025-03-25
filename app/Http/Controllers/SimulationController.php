<?php

namespace App\Http\Controllers;

use App\Models\Note; // Ensure you have a Note model
use Illuminate\Support\Facades\Http;

class SimulationController extends Controller
{
    public function runSimulation($id)
    {
        // Fetch the note by ID
        $note = Note::findOrFail($id);// Throws 404 if not found

        // Check if results exists for this id
        $existing = $note->simulationResults()->latest('completed_at')->first();

        if ($existing) {
            // If result exists, display it immediately
            return redirect("/notes/{$id}");
        }

        $listing_price = (is_null($note->getRawOriginal('listing_price'))) ? 38880 : $note->getRawOriginal('listing_price');

        // Prepare the data to send to the API (adjust based on your table columns)
        $data = [
            "simulations" => 10000,
            "purchase_price" => $listing_price,
            "upb_initial" => $note->getRawOriginal('upb_initial'),
            "monthly_pi" => $note->getRawOriginal('monthly_pi'),
            "term_months" => $note->getRawOriginal('term_months'),
            "interest_rate" => $note->getRawOriginal('interest_rate'),
            "note_id" => $note->note_id,
        ];

        // Send the data to the FastAPI endpoint (POST request)
        try {

            $response = Http::timeout(3600)->post('http://simulation_api:8001/simulation', $data);

            if ($response->successful()) {
                $result = $response->json();

                $note->simulationResults()->create([
                    'note_id' => $note->note_id,
                    'result' => $result,
                    'completed_at' => now(),
                ]);

                return redirect("/notes/{$id}");
            }
            return view('simulation', [
                'result' => ['error' => 'API request failed: ' . $response->status()],
                'note' => $note
            ]);
        } catch (\Exception $e) { // Broader exception to catch all errors
            return view('simulation', [
                'result' => ['error' => 'Exception: ' . $e->getMessage()],
                'note' => $note
            ]);
        }
    }
}
