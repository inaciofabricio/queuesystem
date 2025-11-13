<x-layouts.auth-layout subtitle="{{ empty($subtitle) ? '' : $subtitle }}">

    {!! showMessageSuccess() !!}

    <div class="main-card overflow-auto">

        <div class="flex justify-between items-center">
            <h1 class="title-2">
                Filas de espera
            </h1>
            <h2 class="title-3">
                Empresa: <strong>{{ $companyName }}</strong>
            </h2>
        </div>

        <hr class="mt-2 mb-4">

        <a href="{{ route('queue.create') }}" class="btn">
            <i class="far fa-plus me-2"></i>
            Criar nova fila...
        </a>

        @if (is_null($queues) || count($queues) === 0)

            <div class="text-center my-12 text-gray-500">
                <p class="text-lg">
                    Não existem filas para de espera
                </p>
                <p class="text-sm">
                    Clique no botão acima para criar uma nova fila.
                </p>
            </div>

        @else

            <div class="flex justify-between gap-4 my-4">
                <div class="card-tickets">
                    Filas<br>
                    <strong class="text-3xl">{{ $companyTotal['total_queues'] }}</strong>
                </div>
                <div class="card-tickets">
                    Tickets<br>
                    <strong class="text-3xl">{{ $companyTotal['total_tickets'] }}</strong>
                </div>
                <div class="card-tickets">
                    Aguardando<br>
                    <strong class="text-3xl">{{ $companyTotal['total_waiting'] }}</strong>
                </div>
                <div class="card-tickets">
                    Atendidas<br>
                    <strong class="text-3xl">{{ $companyTotal['total_called'] }}</strong>
                </div>
                <div class="card-tickets">
                    Não atendidas<br>
                    <strong class="text-3xl">{{ $companyTotal['total_not_attended'] }}</strong>
                </div>
                <div class="card-tickets">
                    Dispensadas<br>
                    <strong class="text-3xl">{{ $companyTotal['total_dismissed'] }}</strong>
                </div>
            </div>

            <table id="tabela">
                <thead class="bg-black text-white">
                    <th class="text-xs w-2/14">Nome</th>
                    <th class="text-xs w-2/14">Serviço</th>
                    <th class="text-xs w-2/14">Balcão</th>
                    <th class="text-xs text-center w-1/14">Estado</th>
                    <th class="text-xs text-center w-1/14">Tickets</th>
                    <th class="text-xs text-center w-1/14">Em espera</th>
                    <th class="text-xs text-center w-1/14">Atendidos</th>
                    <th class="text-xs text-center w-1/14">Não atendidos</th>
                    <th class="text-xs text-center w-1/14">Dispensados</th>
                    <th class="text-xs text-center w-2/14">Funcionalidades</th>
                </thead>
                <tbody>
                    @foreach ($queues as $queue)
                    <tr class="{{ $queue->deleted_at ? 'text-red-400' : '' }}">
                        <td>{{ $queue->name }}</td>
                        <td>{{ $queue->service_name }}</td>
                        <td>{{ $queue->service_desk }}</td>
                        @if($queue->deleted_at === null)
                            <td>{!! getQueueStateIcon($queue->status) !!}</td>
                        @else
                            <td><i class="fa-regular fa-trash-can"></i></td>
                        @endif
                        <td>{{ $queue->total_tickets }}</td>
                        <td>{{ $queue->total_waiting }}</td>
                        <td>{{ $queue->total_called }}</td>
                        <td>{{ $queue->total_not_attended }}</td>
                        <td>{{ $queue->total_dismissed }}</td>
                        <td class="flex gap-2 justify-center">

                            @if($queue->deleted_at === null)
                                <a href="{{ route('queue.details', ['id' => Crypt::encrypt($queue->id)]) }}" class="btn-white">
                                    <i class="fa-solid fa-bars" title="Detalhes"></i>
                                </a>
                                <a href="{{ route('queue.edit', ['id' => Crypt::encrypt($queue->id)]) }}" class="btn-white">
                                    <i class="fa-regular fa-pen-to-square" title="Editar"></i>
                                </a>
                                <a href="{{ route('queue.clone', ['id' => Crypt::encrypt($queue->id)]) }}" class="btn-white">
                                    <i class="fa-regular fa-clone" title="Duplicar"></i>
                                </a>
                                <a href="{{ route('queue.delete', ['id' => Crypt::encrypt($queue->id)]) }}" class="btn-red">
                                    <i class="fa-regular fa-trash-can" title="Deletar"></i>
                                </a>
                            @else
                                <a href="{{ route('queue.restore', ['id' => Crypt::encrypt($queue->id)]) }}" class="btn-white">
                                    <i class="fa-solid fa-trash-arrow-up" title="Restaurar"></i>
                                </a>
                            @endif

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        @endif


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const elMessageSuccess = document.getElementById("message-success");

            if (elMessageSuccess) {
                elMessageSuccess.classList.add('show');
                setTimeout(() => elMessageSuccess.classList.remove('show'), 3000);
                setTimeout(() => elMessageSuccess.remove(), 3100);
            }

            $('#tabela').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/pt-BR.json') }}"
                }
            });

        });
    </script>

</x-layouts.auth-layout>
