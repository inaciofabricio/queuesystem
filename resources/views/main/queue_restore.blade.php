<x-layouts.auth-layout subtitle="{{ empty($subtitle) ? '' : $subtitle }}">

    <div class="main-card overflow-auto">
        <h1 class="title-2">
            Restaurar fila de espera
        </h1>
        <hr class="my-4">
        <h2 class="text-slate-600 mb-4 text-center">
            Tem certeza que deseja restaurar a fila de espera?
        </h2>
        <h3 class="text-lg text-zinc-600 font-bold mb-4 text-center">
            {{ $queue->name }}
        </h3>
        <h3 class="text-md text-zinc-600 mb-4 text-center">
            {{ $queue->hash_code }}
        </h3>
        <div class="flex gap-4 justify-center">
            <a href="{{ route('home') }}" class="btn !px-8">Não</a>
            <a href="{{ route('queue.restore.confirm', ['id' => Crypt::encrypt($queue->id)]) }}" class="btn-green !px-8">Sim</a>
        </div>
    </div>

</x-layouts.auth-layout>
