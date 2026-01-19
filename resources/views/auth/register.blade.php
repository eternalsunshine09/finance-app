<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MyInvestment</title>
    @vite('resources/css/app.css')

    <style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <form action="{{ route('register') }}" method="POST"
        class="flex flex-col gap-4 bg-white p-8 w-full max-w-[450px] rounded-[20px] shadow-xl">

        @csrf

        <div class="text-center mb-2">
            <h1 class="text-2xl font-bold text-[#151717]">Buat Akun Baru</h1>
            <p class="text-gray-500 text-sm">Bergabunglah bersama kami hari ini</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="flex flex-col gap-1">
            <label class="font-semibold text-[#151717]">Full Name</label>
            <div
                class="h-[50px] border-[1.5px] border-[#ecedec] rounded-[10px] flex items-center px-3 transition-all duration-200 focus-within:border-[#2d79f3] focus-within:shadow-sm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" class="text-gray-500">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <input type="text" name="name"
                    class="ml-2 w-full h-full border-none outline-none focus:ring-0 bg-transparent placeholder-gray-400"
                    placeholder="John Doe" value="{{ old('name') }}" required>
            </div>
        </div>

        <div class="flex flex-col gap-1">
            <label class="font-semibold text-[#151717]">Email</label>
            <div
                class="h-[50px] border-[1.5px] border-[#ecedec] rounded-[10px] flex items-center px-3 transition-all duration-200 focus-within:border-[#2d79f3] focus-within:shadow-sm">
                <svg height="20" viewBox="0 0 32 32" width="20" class="fill-gray-500">
                    <path
                        d="m30.853 13.87a15 15 0 0 0 -29.729 4.082 15.1 15.1 0 0 0 12.876 12.918 15.6 15.6 0 0 0 2.016.13 14.85 14.85 0 0 0 7.715-2.145 1 1 0 1 0 -1.031-1.711 13.007 13.007 0 1 1 5.458-6.529 2.149 2.149 0 0 1 -4.158-.759v-10.856a1 1 0 0 0 -2 0v1.726a8 8 0 1 0 .2 10.325 4.135 4.135 0 0 0 7.83.274 15.2 15.2 0 0 0 .823-7.455zm-14.853 8.13a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6z">
                    </path>
                </svg>
                <input type="email" name="email"
                    class="ml-2 w-full h-full border-none outline-none focus:ring-0 bg-transparent placeholder-gray-400"
                    placeholder="example@mail.com" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="flex flex-col gap-1">
            <label class="font-semibold text-[#151717]">Password</label>
            <div
                class="h-[50px] border-[1.5px] border-[#ecedec] rounded-[10px] flex items-center px-3 transition-all duration-200 focus-within:border-[#2d79f3] focus-within:shadow-sm">
                <svg height="20" viewBox="-64 0 512 512" width="20" class="fill-gray-500">
                    <path
                        d="m336 512h-288c-26.453125 0-48-21.523438-48-48v-224c0-26.476562 21.546875-48 48-48h288c26.453125 0 48 21.523438 48 48v224c0 26.476562-21.546875 48-48 48zm-288-288c-8.8125 0-16 7.167969-16 16v224c0 8.832031 7.1875 16 16 16h288c8.8125 0 16-7.167969 16-16v-224c0-8.832031-7.1875-16-16-16zm0 0">
                    </path>
                </svg>
                <input type="password" name="password"
                    class="ml-2 w-full h-full border-none outline-none focus:ring-0 bg-transparent placeholder-gray-400"
                    placeholder="Create Password" required>
            </div>
        </div>

        <div class="flex flex-col gap-1">
            <label class="font-semibold text-[#151717]">Confirm Password</label>
            <div
                class="h-[50px] border-[1.5px] border-[#ecedec] rounded-[10px] flex items-center px-3 transition-all duration-200 focus-within:border-[#2d79f3] focus-within:shadow-sm">
                <svg height="20" viewBox="-64 0 512 512" width="20" class="fill-gray-500">
                    <path
                        d="m336 512h-288c-26.453125 0-48-21.523438-48-48v-224c0-26.476562 21.546875-48 48-48h288c26.453125 0 48 21.523438 48 48v224c0 26.476562-21.546875 48-48 48zm-288-288c-8.8125 0-16 7.167969-16 16v224c0 8.832031 7.1875 16 16 16h288c8.8125 0 16-7.167969 16-16v-224c0-8.832031-7.1875-16-16-16zm0 0">
                    </path>
                </svg>
                <input type="password" name="password_confirmation"
                    class="ml-2 w-full h-full border-none outline-none focus:ring-0 bg-transparent placeholder-gray-400"
                    placeholder="Repeat Password" required>
            </div>
        </div>

        <button type="submit"
            class="mt-4 bg-[#151717] hover:bg-[#252727] text-white font-medium rounded-[10px] h-[50px] w-full transition-colors duration-200">
            Sign Up
        </button>

        <p class="text-center text-sm text-gray-600 mt-2">
            Already have an account?
            <a href="{{ route('login') }}" class="text-[#2d79f3] font-medium hover:underline ml-1">Sign In</a>
        </p>
    </form>
</body>

</html>