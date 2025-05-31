<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tailwind Deep Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-center text-blue-700 mb-8">Tailwind Full Layout Test</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <h2 class="text-2xl font-semibold text-gray-800">Sample Card</h2>
                <p class="text-gray-600">This card has padding, shadow, and rounded corners. Text is styled using
                    Tailwind.</p>
                <button class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Click Me</button>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <form class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Form Section</h2>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Name</label>
                        <input type="text"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Email</label>
                        <input type="email"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
