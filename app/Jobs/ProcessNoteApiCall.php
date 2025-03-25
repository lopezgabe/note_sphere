<?php

namespace App\Jobs;

use App\Models\NoteApiResponse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessNoteApiCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $note;

    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    public function handle()
    {
        // Prepare data to send to API (example)
        $data = [
            "simulations" => 100,
            "purchase_price" => 10.01,
            "upb_initial" => 20,
            "monthly_pi" => 10,
            "term_months" => 360,
            "interest_rate" => ".04",
            "note_id" => "Note ID Address"
        ];

        try {
            // Call the API (replace with your actual API endpoint)
            $response = Http::post('http://localhost/simulation', $data);

            // Check if the API call was successful
            if ($response->successful()) {
                NoteApiResponse::updateOrCreate(
                    ['note_id' => $this->note->id],
                    [
                        'api_response' => $response->json(),
                        'status' => 'success',
                    ]
                );
            } else {
                $this->fail(new \Exception('API call failed: ' . $response->status()));
            }
        } catch (\Exception $e) {
            // Log error and mark as failed
            NoteApiResponse::updateOrCreate(
                ['note_id' => $this->note->id],
                [
                    'api_response' => ['error' => $e->getMessage()],
                    'status' => 'failed',
                ]
            );
            throw $e; // Re-throw to mark job as failed
        }
    }
}
