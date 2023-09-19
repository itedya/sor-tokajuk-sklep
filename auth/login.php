<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logowanie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-900 min-h-screen w-full flex justify-center items-center">
<form method="POST" action="/api/auth/login" class="w-full max-w-xl p-4 flex flex-col gap-8">
    <h1 class="text-4xl font-bold text-center text-neutral-400">Logowanie</h1>

    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <label for="email" class="text-lg text-neutral-300 font-semibold mx-2">Email</label>
            <input type="email" name="email" id="email"
                   class="p-4 bg-neutral-800 rounded-xl border-neutral-700 focus:outline-none text-lg text-neutral-300"/>
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Hasło</label>
            <input type="password" name="password" id="password"
                   class="p-4 bg-neutral-800 rounded-xl border-neutral-700 focus:outline-none text-lg text-neutral-300"/>
        </div>

        <div class="flex justify-end">
            <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zaloguj</button>
        </div>
    </div>
</form>
</body>
</html>