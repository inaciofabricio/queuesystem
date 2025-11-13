<x-layouts.auth-layout subtitle="{{ empty($subtitle) ? '' : $subtitle }}">

    <div class="main-card overflow-auto">

        <div class="flex justify-between items-center">
            <p class="title-2">Detalhes da fila de espera</p>
            <a href="{{ route('home') }}" class="btn"><i class="fa-solid fa-arrow-left me-2"></i>Voltar</a>
        </div>

        <hr class="mt-2 mb-4">

        <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-full p-2 mb-4">
            <span>
                Nome:
            </span>
            <span class="text-black font-semibold">
                {{ $queue->name }}
            </span>
        </p>
        <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-full p-2 mb-4">
            <span>
                Descrição:
            </span>
            <span class="text-black font-semibold">
                {{ $queue->description }}
            </span>
        </p>

        <div class="flex gap-4 mb-4">
            <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-full p-2">
                <span>
                    Serviço:
                </span>
                <span class="text-black font-semibold">
                    {{ $queue->service_name }}
                </span>
            </p>
            <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-full p-2">
                <span>
                    Balcão:
                </span>
                <span class="text-black font-semibold">
                    {{ $queue->service_desk }}
                </span>
            </p>
            <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-full p-2">
                <span>
                    Formato:
                </span>
                <span class="text-black font-semibold">
                    {{ getFormattedTicketNumber(0, $queue->queue_prefix, $queue->queue_total_digits) }}
                </span>
            </p>
        </div>

        <div class="flex gap-4 mb-4">
            <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-3/14 p-2">
                <span>
                    Estado:
                </span>
                <span class="text-black font-semibold">
                    {{ getQueueStateText($queue->status) }}
                </span>
            </p>
            <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-3/14 p-2">
                <span>
                    Criada em:
                </span>
                <span class="text-black font-semibold">
                    {{ formatDateBr($queue->created_at) }}
                </span>
            </p>
            <p class="bg-zinc-100 border-1 border-slate-300 rounded-md w-full p-2">
                <span>
                    Código:
                </span>
                <span class="text-black font-semibold">
                    {{ $queue->hash_code }}
                </span>
            </p>
        </div>

        <hr class="mt-2 mb-4">

        <p class="title-2">Tickets da fila de espera</p>

        @if (is_null($tickets) || count($tickets) === 0)
            <p class="text-xl text-center text-gray-400 my-12">Não existem <i>tickets</i> nesta fila.</p>
        @else

            <div class="flex justify-between gap-4 my-4">
                <div class="card-tickets">
                    Total<br>
                    <strong class="text-3xl">{{ $queue->total_tickets }}</strong>
                </div>
                <div class="card-tickets">
                    Aguardando<br>
                    <strong class="text-3xl">{{ $queue->total_waiting }}</strong>
                </div>
                <div class="card-tickets">
                    Atendidas<br>
                    <strong class="text-3xl">{{ $queue->total_called }}</strong>
                </div>
                <div class="card-tickets">
                    Não atendidas<br>
                    <strong class="text-3xl">{{ $queue->total_not_attended }}</strong>
                </div>
                <div class="card-tickets">
                    Dispensadas<br>
                    <strong class="text-3xl">{{ $queue->total_dismissed }}</strong>
                </div>
            </div>

            <table id="table-tickets">
                <thead class="bg-black text-white">
                    <tr>
                        <th class="w-1/10">Número</th>
                        <th class="w-2/10">Criada em</th>
                        <th class="w-2/10">Estado</th>
                        <th class="w-2/10">Chamada em</th>
                        <th class="w-3/10">Atendida por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        <tr class="border-1 border-slate-300">
                            <td class="border-1 border-slate-300 text-center">
                                {{
                                    getFormattedTicketNumber(
                                        $ticket->queue_ticket_number,
                                        $queue->queue_prefix,
                                        $queue->queue_total_digits)
                                }}
                            </td>
                            <td class="border-1 border-slate-300 text-center">
                                {{ formatDateBr($ticket->queue_ticket_created_at) }}
                            </td>
                            <td class="border-1 border-slate-300 text-center">
                                {{ getTicketStateText($ticket->queue_ticket_status) }}
                            </td>
                            <td class="border-1 border-slate-300 text-center">
                                {{ formatDateBr($ticket->queue_ticket_called_at) }}
                            </td>
                            <td class="border-1 border-slate-300 text-center">
                                {{ $ticket->queue_ticket_called_by ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

    <script>
        $(document).ready(function() {
            $('#table-tickets').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/pt-BR.json') }}"
                },
            });
        });
    </script>


</x-layouts.auth-layout>
