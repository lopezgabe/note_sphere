<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Note;
use App\Models\SimulationResult;

class SimulationController extends Controller
{
    public function runSimulation($id)
    {
        $note = Note::findOrFail($id);

        if (request()->isMethod('get')) {
            return view('simulation', [
                'note' => $note,
                'scenarios' => $note->simulationResults()->latest('completed_at')->get(),
                'result' => null,
            ]);
        }

        // Validate inputs
        $validated = request()->validate([
            'name' => 'nullable|string|max:255',
            'parameters.simulations' => 'required|integer|min:1',
            'parameters.purchase_price' => 'required|numeric|min:0',
            'parameters.upb_initial' => 'required|numeric|min:0',
            'parameters.monthly_pi' => 'required|numeric|min:0',
            'parameters.term_months' => 'required|integer|min:1',
            'parameters.interest_rate' => 'required|numeric|min:0|max:100',
        ]);

        $parameters = $validated['parameters'];
        $name = $validated['name'] ?? 'Unnamed Scenario';

        // Base data from note
        $data = [
            'simulations' => 10000,
            'purchase_price' => $note->getRawOriginal('listing_price'),
            'upb_initial' => $note->getRawOriginal('upb_initial'),
            'monthly_pi' => $note->getRawOriginal('monthly_pi'),
            'term_months' => $note->getRawOriginal('term_months'),
            'interest_rate' => $note->getRawOriginal('interest_rate'),
            'note_id' => $note->note_id,
        ];

        // Override with user-provided parameters
        $data = array_merge($data, $parameters);

        try {

            $response = Http::timeout(3600)->post('http://simulation_api:8001/simulation', $data);
            $amount = $note->getRawOriginal('listing_price') ?? $note->getRawOriginal('upb_initial');
            $example_scenarios = SimulationResult::getExampleScenarios($amount);

            if ($response->successful()) {
                $result = $response->json();
                $analysis = SimulationResult::analyzeResult($result);

                $simulationResult = $note->simulationResults()->create([
                    'name' => $name,
                    'parameters' => $parameters,
                    'result' => $result,
                    'analysis' => $analysis,
                    'example_scenarios' => $example_scenarios,
                    'completed_at' => now(),
                ]);
                return redirect()->route('simulation.show', $note->id);
            }

            return view('simulation', [
                'result' => ['error' => 'API request failed: ' . $response->status()],
                'note' => $note,
                'scenarios' => $note->simulationResults()->latest('completed_at')->get(),
                'example_scenarios' => $example_scenarios,
            ]);
        } catch (\Exception $e) {
            return view('simulation', [
                'result' => ['error' => 'Exception: ' . $e->getMessage()],
                'note' => $note,
                'scenarios' => $note->simulationResults()->latest('completed_at')->get(),
            ]);
        }
    }

    public function deleteScenario($id)
    {
        $scenario = SimulationResult::findOrFail($id);
        $noteId = $scenario->note_id;
        $scenario->delete();
        return redirect()->route('simulation.show', $noteId)->with('success', 'Scenario deleted.');
    }

    public function toggleFavorite($id)
    {
        $note = Note::findOrFail($id);
        $note->update(['is_favorite' => !$note->is_favorite]);
        return redirect()->back()->with('success', $note->is_favorite ? 'Note marked as favorite.' : 'Note unmarked as favorite.');
    }

    public function toggleAvoid($id)
    {
        $note = Note::findOrFail($id);
        $note->update(['is_avoid' => !$note->is_avoid]);
        return redirect()->back()->with('success', $note->is_avoid ? 'Note marked to avoid.' : 'Note unmarked to avoid.');
    }

    public function updateNotes($id)
    {
        $note = Note::findOrFail($id);
        $note->update(['notes' => request('notes')]);
        return redirect()->back()->with('success', 'Notes updated successfully.');
    }

}
