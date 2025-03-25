
<x-app-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Notes</h1>

        @if (session('message'))
            <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <a href="{{ route('notes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Create Note</a>

        <table class="w-full mt-4 border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Title</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notes as $note)
                    <tr class="border">
                        <td class="p-2">{{ $note->title }}</td>
                        <td class="p-2">
                            <a href="{{ route('notes.edit', $note) }}" class="text-blue-500">Edit</a> |
                            <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $notes->links() }}
    </div>
</x-app-layout>
