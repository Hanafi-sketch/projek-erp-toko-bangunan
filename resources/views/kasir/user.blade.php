@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold text-gray-800 leading-tight">Data Pengguna</h2>
@endsection

@section('content')
<div class="py-6 max-w-7xl mx-auto">
    <div class="bg-white shadow-md rounded p-6 overflow-x-auto" x-data="{ openModal: false, selectedUser: {} }">
        <h3 class="text-lg font-semibold mb-4">Daftar Akun</h3>

        <table class="min-w-full table-auto border border-gray-300">
            <thead class="bg-gray-100 text-center">
                <tr>
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">Nama</th>
                    <th class="p-2 border">Email</th>
                    <th class="p-2 border">Terdaftar Pada</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse ($users as $index => $user)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-2 border">{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                        <td class="p-2 border">{{ $user->name }}</td>
                        <td class="p-2 border">{{ $user->email }}</td>
                        <td class="p-2 border">{{ $user->created_at->format('d-m-Y H:i') }}</td>
                        <td class="p-2 border space-x-2">
                            <button 
                                @click.prevent="openModal = true; selectedUser = {{ $user->toJson() }}"
                                class="bg-yellow-500 hover:bg-yellow-600 text-black text-xs px-3 py-1 rounded shadow">
                                Edit
                            </button>
                            <form action="{{ route('kasir.users.destroy', $user->id) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded shadow">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-gray-500 py-4">Belum ada data pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

        <!-- Modal Edit -->
        <div x-show="openModal" x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-lg font-semibold mb-4">Edit Pengguna</h2>
                <form method="POST" :action="`{{ url('kasir/users') }}/${selectedUser.id}`">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" class="border px-3 py-2 rounded w-full mt-1"
                               x-model="selectedUser.name" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" class="border px-3 py-2 rounded w-full mt-1"
                               x-model="selectedUser.email" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="border px-3 py-2 rounded w-full mt-1"
                               placeholder="Biarkan kosong jika tidak diubah">
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="openModal = false"
                                class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">
                            Batal
                        </button>
                        <button type="submit" class="btn-save">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
