<?php

namespace App\Exports;

use App\Models\Note;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NotesExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return Note::select('id', 'url', 'listing_type')->get();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array {
        return ['ID', 'URL', 'Listing Type'];
    }
}
