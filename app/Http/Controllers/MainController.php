<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\QueueTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function index() {

        $data = [
            'subtitle' => 'Home',
            'queues' => $this->getQueuesList(),
            'companyName' => Auth::user()->company->company_name,
            'companyTotal' => $this->getCompanyTotals()
        ];

        return view('main.home', $data);
    }

    private function getQueuesList() {

        $companyID = Auth::user()->id_company;

        return Queue::withTrashed()
            ->where('id_company', $companyID)
            // ->where('status', 'active')
            // ->whereNull('deleted_at')
            ->withCount([
                //'waiting','called','not_attended','dismissed'
                'tickets as total_tickets' => function ($query) {
                    $query->whereNotNull('queue_ticket_status');
                },
                'tickets as total_dismissed' => function ($query) {
                    $query->where('queue_ticket_status', 'dismissed');
                },
                'tickets as total_not_attended' => function ($query) {
                    $query->where('queue_ticket_status', 'not_attended');
                },
                'tickets as total_called' => function ($query) {
                    $query->where('queue_ticket_status', 'called');
                },
                'tickets as total_waiting' => function ($query) {
                    $query->where('queue_ticket_status', 'waiting');
                }
            ])
            ->get();
    }

    public function queueDetails($id) {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID da fila inválido.');
        }

        $queue = Queue::where('id', $id)
        ->where('id_company', Auth::user()->id_company)
        ->withCount([
            'tickets as total_tickets' => function($query) {
                $query->whereNotNull('queue_ticket_status')
                    ->whereNull('deleted_at');
            },
            'tickets as total_dismissed' => function($query) {
                $query->where('queue_ticket_status', 'dismissed')
                    ->whereNull('deleted_at');
            },
            'tickets as total_not_attended' => function($query) {
                $query->where('queue_ticket_status', 'not_attended')
                    ->whereNull('deleted_at');
            },
            'tickets as total_called' => function($query) {
                $query->where('queue_ticket_status', 'called')
                    ->whereNull('deleted_at');
            },
            'tickets as total_waiting' => function($query) {
                $query->where('queue_ticket_status', 'waiting')
                    ->whereNull('deleted_at');
            }
        ])
        ->firstOrFail();

        if (!$queue) {
            abort(404, 'Fila não encontrada.');
        }

        $tickets = $queue->tickets()->get();

        $data = [
            'dubtitle' => 'Detalhes',
            'queue' => $queue,
            'tickets' => $tickets
        ];

        return view('main.queue_details', $data);
    }

    private function getCompanyTotals() {

        $companyID = Auth::user()->id_company;
        $totalQueues = Queue::where('id_company', $companyID)->count();

        $tickets = QueueTicket::whereHas('queue', function($queue) use ($companyID) {
            $queue->where('id_company', $companyID);
        })->get();

        return [
            'total_queues' => $totalQueues,
            'total_tickets' => $tickets->count(),
            'total_dismissed' => $tickets->where('queue_ticket_status', 'dismissed')->count(),
            'total_not_attended' => $tickets->where('queue_ticket_status', 'not_attended')->count(),
            'total_called' => $tickets->where('queue_ticket_status', 'called')->count(),
            'total_waiting' => $tickets->where('queue_ticket_status', 'waiting')->count(),
        ];
    }

    public function createQueue() {

        $data = [
            'subtitle' => 'Criar fila'
        ];

        return view('main.queue_create_frm', $data);
    }

    public function createQueueSubmit(Request $request) {

        $request->validate(
            [
                'name' => 'required|min:5|max:255',
                'description' => 'required|min:5|max:255',
                'service' => 'required|min:3|max:50',
                'desk' => 'required|min:1|max:20',
                'prefix' => 'required|regex:/^[A-Z\-]{1}$/',
                'total_digits' => 'required|integer|min:2|max:4',
                'color_1' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'color_2' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'color_3' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'color_4' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'hidden_hash_code' => 'required|size:64',
                'status' => 'required|in:active,inactive',
            ],
            [
                'name.required' => 'O Nome da fila é obrigatório.',
                'name.min' => 'O Nome da fila deve ter pelo menos 5 caracteres.',
                'name.max' => 'O Nome da fila não pode ter mais que 255 caracteres.',
                'description.required' => 'A Descrição da fila é obrigatória.',
                'description.min' => 'A Descrição da fila deve ter pelo menos 5 caracteres.',
                'description.max' => 'A Descrição da fila não pode ter mais que 255 caracteres.',
                'service.required' => 'O Serviço da fila é obrigatório.',
                'service.min' => 'O Serviço da fila deve ter pelo menos 3 caracteres.',
                'service.max' => 'O Serviço da fila não pode ter mais que 50 caracteres.',
                'desk.required' => 'O Balcão é obrigatório.',
                'desk.min' => 'O Balcão deve ter pelo menos 1 caractere.',
                'desk.max' => 'O Balcão não pode ter mais que 20 caracteres.',
                'prefix.required' => 'O Prefixo é obrigatório.',
                'prefix.regex' => 'O Prefixo não tem o valor correto.',
                'total_digits.required' => 'O Total de digitos é obrigatório.',
                'Total_digits.integer' => 'O Total de digitos deve ser um número inteiro.',
                'color_1.required' => 'O Prefixo - Cor de fundo é obrigatório.',
                'color_1.regex' => 'O Prefixo - Cor de fundo deve ser um código hexdecimal válido (ex: #ffffff).',
                'color_2.required' => 'O Prefixo - Cor do texto é obrigatória.',
                'color_2.regex' => 'O Prefixo - Cor do texto deve ser um código hexdecimal válido (ex: #ffffff).',
                'color_3.required' => 'O Número - Cor de fundo obrigatória.',
                'color_3.regex' => 'O Número - Cor de fundo deve ser um código hexdecimal válido (ex: #ffffff).',
                'color_4.required' => 'O Número - Cor do texto é obrigatória.',
                'color_4.regex' => 'O Número - Cor do texto deve ser um código hexdecimal válido (ex: #ffffff).',
                'hidden_hash_code.required' => 'O código hash é obrigatório.',
                'hidden_hash_code.size' => 'O código hash deve ter exatamente 64 caracteres.',
                'status.required' => 'O Estado da fila é obrigatório.',
                'status.in' => 'O Estado da fila deve ser ativo ou inativo.',
            ]
        );

        $companyID = Auth::user()->id_company;
        $queueExists = Queue::where('id_company', $companyID)
            ->where('name', trim($request->name))
            ->exists();

        if($queueExists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('server_error', 'Já existe uma fila de espera com esse nome. Por favor defina um nome diferente.');
        }

        $hashCode = $request->hidden_hash_code;
        $hashExists = Queue::where('hash_code', $hashCode)->exists();

        if($hashExists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('server_error', 'O código hash da fila já existe. Por favor gere um novo codigo.');
        }

        $newQueue = new Queue();

        $newQueue->id_company = Auth::user()->id_company;
        $newQueue->name = trim($request->name);
        $newQueue->description = trim($request->description);
        $newQueue->service_name = trim($request->service);
        $newQueue->service_desk = trim($request->desk);
        $newQueue->queue_prefix = strtoupper(trim($request->prefix));
        $newQueue->queue_total_digits = (int) trim($request->total_digits);
        $newQueue->queue_colors = json_encode([
            'prefix_bg_color' => trim($request->color_1),
            'prefix_text_color' => trim($request->color_2),
            'number_bg_color' => trim($request->color_3),
            'number_text_color' => trim($request->color_4)
        ]);
        $newQueue->hash_code = trim($request->hidden_hash_code);
        $newQueue->status = trim($request->status);

        $newQueue->save();

        return redirect()->route('home');

    }

    public function generateQueuehash() {

        $hash = hash('sha256', Str::random(40));

        while(Queue::where('hash_code', $hash)->exists()) {
            $hash = hash('sha256', Str::random(40));
        }

        return response()->json(['hash' => $hash]);
    }

    public function editQueue($id) {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID da fila inválido.');
        }

        $queue = Queue::where('id', $id)
            ->where('id_company', Auth::user()->id_company)
            ->firstOrFail();

        if(!$queue) {
            abort(404, 'Fila não encontrada.');
        }

        $data = [
            'sutitle' => 'Editar fila',
            'queue' => $queue,
            'queueColors' => json_decode($queue->queue_colors, true)
        ];

        return view('main.queue_edit_frm', $data);
    }

    public function editQueueSubmit(Request $request) {

        $request->validate(
            [
                'name' => 'required|min:5|max:255',
                'description' => 'required|min:5|max:255',
                'service' => 'required|min:3|max:50',
                'desk' => 'required|min:1|max:20',
                'prefix' => 'required|regex:/^[A-Z\-]{1}$/',
                'color_1' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'color_2' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'color_3' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'color_4' => 'required|regex:/^\#[a-fA-F0-9]{6}$/',
                'status' => 'required|in:active,inactive',
            ],
            [
                'name.required' => 'O Nome da fila é obrigatório.',
                'name.min' => 'O Nome da fila deve ter pelo menos 5 caracteres.',
                'name.max' => 'O Nome da fila não pode ter mais que 255 caracteres.',
                'description.required' => 'A Descrição da fila é obrigatória.',
                'description.min' => 'A Descrição da fila deve ter pelo menos 5 caracteres.',
                'description.max' => 'A Descrição da fila não pode ter mais que 255 caracteres.',
                'service.required' => 'O Serviço da fila é obrigatório.',
                'service.min' => 'O Serviço da fila deve ter pelo menos 3 caracteres.',
                'service.max' => 'O Serviço da fila não pode ter mais que 50 caracteres.',
                'desk.required' => 'O Balcão é obrigatório.',
                'desk.min' => 'O Balcão deve ter pelo menos 1 caractere.',
                'desk.max' => 'O Balcão não pode ter mais que 20 caracteres.',
                'total_digits.required' => 'O Total de digitos é obrigatório.',
                'Total_digits.integer' => 'O Total de digitos deve ser um número inteiro.',
                'color_1.required' => 'O Prefixo - Cor de fundo é obrigatório.',
                'color_1.regex' => 'O Prefixo - Cor de fundo deve ser um código hexdecimal válido (ex: #ffffff).',
                'color_2.required' => 'O Prefixo - Cor do texto é obrigatória.',
                'color_2.regex' => 'O Prefixo - Cor do texto deve ser um código hexdecimal válido (ex: #ffffff).',
                'color_3.required' => 'O Número - Cor de fundo obrigatória.',
                'color_3.regex' => 'O Número - Cor de fundo deve ser um código hexdecimal válido (ex: #ffffff).',
                'color_4.required' => 'O Número - Cor do texto é obrigatória.',
                'color_4.regex' => 'O Número - Cor do texto deve ser um código hexdecimal válido (ex: #ffffff).',
                'status.required' => 'O Estado da fila é obrigatório.',
                'status.in' => 'O Estado da fila deve ser ativo ou inativo.',
            ]
        );

        try {
            Crypt::decrypt($request->queue_id);
        } catch (\Exception $e) {
            abort(403, 'Operação inválida.');
        }

        $queueId = Crypt::decrypt($request->queue_id);
        $companyId = Auth::user()->id_company;

        $queue = Queue::where('id', $queueId)
            ->where('id_company', $companyId)
            ->firstOrFail();

        if(!$queue) {
            abort(404, 'Operação inválida.');
        }

        $queueExists = Queue::where('id_company', $companyId)
            ->where('name', trim($request->name))
            ->where('id', '!=', $queueId)
            ->exists();

        if($queueExists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('server_error', 'Já existe outra fila de espera com esse nome. Por favor defina um nome diferente.');
        }

        $queue->name = trim($request->name);
        $queue->description = trim($request->description);
        $queue->service_name = trim($request->service);
        $queue->service_desk = trim($request->desk);
        $queue->queue_prefix = strtoupper(trim($request->prefix));
        $queue->queue_colors = json_encode([
            'prefix_bg_color' => trim($request->color_1),
            'prefix_text_color' => trim($request->color_2),
            'number_bg_color' => trim($request->color_3),
            'number_text_color' => trim($request->color_4)
        ]);
        $queue->status = trim($request->status);

        $queue->save();

        return redirect()->route('home')->with(['message_success' => 'Fila de espera editada com sucesso']);
    }

    public function cloneQueue($id) {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID da fila inválido.');
        }

        $queue = Queue::where('id', $id)
            ->where('id_company', Auth::user()->id_company)
            ->firstOrFail();

        if(!$queue) {
            abort(403, 'Fila não encontrada.');
        }

        $data = [
            'subtitle' => 'Clonar Fila',
            'queue' => $queue
        ];

        return view('main.queue_clone_frm', $data);
    }

    public function cloneQueueSubmit(Request $request) {

        $request->validate(
            [
                'name' => 'required|min:5|max:255'
            ],
            [
                'name.required' => 'O Nome da fila é obrigatório.',
                'name.min' => 'O Nome da fila deve ter pelo menos 5 caracteres.',
                'name.max' => 'O Nome da fila não pode ter mais que 255 caracteres.'
            ]
        );

        try {
            Crypt::decrypt($request->original_queue_id);
        } catch (\Exception $e) {
            abort(403, 'Operação inválida.');
        }

        $originalQueueId = Crypt::decrypt($request->original_queue_id);
        $companyId = Auth::user()->id_company;

        $queue = Queue::where('id', $originalQueueId)
            ->where('id_company', $companyId)
            ->firstOrFail();

        if(!$queue) {
            abort(403, 'Operação inválida.');
        }

        $queueExists = Queue::where('id_company', $companyId)
            ->where('name', trim($request->name))
            ->exists();

        if($queueExists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('server_error', 'Já existe outra fila de espera com esse nome. Por favor defina um nome diferente.');
        }


        $newQueue = new Queue();


        $newQueue->id_company = $queue->id_company;
        $newQueue->name = trim($request->name);
        $newQueue->description = $queue->description;
        $newQueue->service_name = $queue->service_name;
        $newQueue->service_desk = $queue->service_desk;
        $newQueue->queue_prefix = $queue->queue_prefix;
        $newQueue->queue_total_digits = $queue->queue_total_digits;
        $newQueue->queue_colors = $queue->queue_colors;
        $newQueue->status = $queue->status;

        $hash = hash('sha256', Str::random(40));

        while(Queue::where('hash_code', $hash)->exists()) {
            $hash = hash('sha256', Str::random(40));
        }

        $newQueue->hash_code = $hash;

        $newQueue->save();

        return redirect()->route('home')->with('message_success', 'Fila de espera clonada com sucesso');
    }

    public function deleteQueue($id) {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID da fila inválido.');
        }

        $queue = Queue::where('id', $id)
            ->where('id_company', Auth::user()->id_company)
            ->firstOrFail();

        if(!$queue) {
            abort(403, 'Fila não encontrada.');
        }

        $data = [
            'subtitle' => 'Deletar Fila',
            'queue' => $queue
        ];

        return view('main.queue_delete', $data);
    }

    public function deleteConfirmQueue($id) {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID da fila inválido.');
        }

        $queue = Queue::where('id', $id)
        ->where('id_company', Auth::user()->id_company)
        ->firstOrFail();

        if(!$queue) {
            abort(403, 'Fila não encontrada.');
        }

        $queue->delete();

        return redirect()->route('home')->with('message_success', 'Fila de espera deletada com sucesso');
    }

    public function restoreQueue($id) {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID da fila inválido.');
        }

        $queue = Queue::withTrashed()
            ->where('id', $id)
            ->where('id_company', Auth::user()->id_company)
            ->firstOrFail();

        if(!$queue) {
            abort(403, 'Fila não encontrada.');
        }

        $data = [
            'subtitle' => 'Deletar Fila',
            'queue' => $queue
        ];

        return view('main.queue_restore', $data);
    }

    public function restoreConfirmQueue($id) {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID da fila inválido.');
        }

        $queue = Queue::withTrashed()
            ->where('id', $id)
            ->where('id_company', Auth::user()->id_company)
            ->firstOrFail();

        if(!$queue) {
            abort(403, 'Fila não encontrada.');
        }

        $queue->restore();

        return redirect()->route('home')->with('message_success', 'Fila de espera restaurada com sucesso');
    }
}
