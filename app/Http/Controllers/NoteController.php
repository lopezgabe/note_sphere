<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NotesExport;
use App\Imports\NotesImport;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        $notes = Note::paginate(15);

        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        return view('notes.create');
    }

    public function show(Note $note)
    {
        return view('notes.show', ['note' => $note]);
    }

    public function store()
    {
//        request()->validate([
//            'title' => ['required', 'min:3'],
//            'salary' => ['required']
//        ]);
//
//        $job = Note::create([
//            'title' => request('title'),
//            'salary' => request('salary'),
//            'employer_id' => 1
//        ]);
//
//        Mail::to($job->employer->user)->queue(
//            new JobPosted($job)
//        );

        return redirect('/notes');
    }

    public function edit(Note $note)
    {
        return view('notes.edit', ['note' => $note]);
    }

    public function update(Note $note)
    {
        Gate::authorize('edit-note', $note);

        request()->validate([
            'title' => ['required', 'min:3'],
            'salary' => ['required']
        ]);

        $note->update([
            'title' => request('title'),
            'salary' => request('salary'),
        ]);

        return redirect('/notes/' . $note->id);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return Excel::download(new NotesExport, 'notes.xlsx');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function import(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'file' => 'required|max:2048',
        ]);

        Excel::import(new NotesImport, $request->file('file'));

        return back()->with('success', 'Notes imported successfully.');
    }
}
