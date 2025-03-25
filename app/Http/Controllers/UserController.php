<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\User;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function index()
    {
        $users = User::get();

        return view('users', compact('users'));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
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

        Excel::import(new UsersImport, $request->file('file'));

        return back()->with('success', 'Users imported successfully.');
    }
}
