<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email Address</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-md mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Welcome to Multi-Tenant SaaS 👋</h2>
            <p class="text-gray-600 mb-4">
                Hi {{ $user->name }},
            </p>
            <p class="text-gray-700 mb-4">
                Thanks for signing up! Please verify your email address by clicking the button below:
            </p>

            <div class="text-center">
                <a href="{{ $url }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
                    Verify Email
                </a>
            </div>

            <p class="text-gray-600 mt-6 text-sm">
                If the button above doesn't work, copy and paste this link into your browser:
            </p>
            <p class="text-gray-500 text-sm break-words">
                {{ $url }}
            </p>

            <p class="text-gray-700 mt-6">
                After verification, you can log in <a href="{{ $login_url }}" class="text-blue-600 underline">here</a>.
            </p>

            <p class="text-gray-500 text-sm mt-4">
                If you did not create an account, no further action is required.
            </p>
        </div>

        <div class="bg-gray-100 px-6 py-4 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Multi-Tenant SaaS. All rights reserved.
        </div>
    </div>
</body>
</html>
