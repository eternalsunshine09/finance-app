<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-800 p-4 shadow-lg text-white mb-8">
        <div class="container mx-auto font-bold">
            <a href="{{ route('dashboard') }}">⬅ Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container mx-auto px-4">

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4 text-gray-700">👤 Edit Profil</h2>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PATCH')

                    <div class="mb-4 text-center">
                        @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                            class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-blue-100">
                        @else
                        <div
                            class="w-24 h-24 rounded-full mx-auto bg-gray-300 flex items-center justify-center text-gray-500 text-2xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1">Ganti Foto</label>
                        <input type="file" name="avatar" class="w-full text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full border rounded p-2"
                            required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-1">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="w-full border rounded p-2"
                            required>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700">
                        Simpan Profil
                    </button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg h-fit">
                <h2 class="text-xl font-bold mb-4 text-gray-700">🔒 Ganti Password</h2>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1">Password Lama</label>
                        <input type="password" name="current_password" class="w-full border rounded p-2" required>
                        @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1">Password Baru</label>
                        <input type="password" name="password" class="w-full border rounded p-2" required>
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-1">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-gray-700 text-white font-bold py-2 rounded hover:bg-gray-800">
                        Update Password
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>

</html>