<?php

if(!function_exists('showValidarionError')) {

    function showValidarionError($fieldName, $validationErrors) {

        if($validationErrors->has($fieldName)) {
            return '<div class="text-sm italic text-red-500">' . $validationErrors->first($fieldName) . '</div>';
        } else {
            return '';
        }
    }
}

if(!function_exists('showServerError')) {

    function showServerError() {

        if(session()->has('server_error')) {
            return '<div class="text-sm italic text-red-500 flex justify-center">' . session()->get('server_error') . '</div>';
        } else {
            return '';
        }
    }
}

if(!function_exists('showMessageSuccess')) {

    function showMessageSuccess() {

        if(session()->has('message_success')) {
            return '<div id="message-success" class="card-message-success">' . session()->get('message_success') . '</div>';
        } else {
            return '';
        }
    }
}

if (!function_exists('getFormattedTicketNumber')) {

    function getFormattedTicketNumber($ticketNumber, $prefix = null, $totalDigits = 3) {

        $result = '';

        if($prefix) {
            $result = $prefix;
        }

        if($totalDigits > 0) {
            $result .= str_pad($ticketNumber, $totalDigits, '0', STR_PAD_LEFT);
        }

        return $result;
    }
}

if (!function_exists('getTicketStateText')) {

    function getTicketStateText($state) {

        $rules = [
            'waiting' => 'Aguardando',
            'called' => 'Atendido',
            'not_attended' => 'Não atendido',
            'dismissed' => 'Dispensado',
        ];

        return $rules[$state] ?? 'Desconhecido';
    }
}

if (!function_exists('getQueueStateIcon')) {

    function getQueueStateIcon($state) {

        $icons = [
            'active' => '<i class="fa-regular fa-circle-check text-green-700" title="Ativa"></i>',
            'inactive' => '<i class="fa-regular fa-circle-xmark text-red-700" title="Inativa"></i>',
            'done' => '<i class="fa-solid fa-ban text-slate-300" title="Concluída"></i>'
        ];

        return $icons[$state] ?? '-';
    }
}

if (!function_exists('getQueueStateText')) {

    function getQueueStateText($state) {

        $rules = [
            'active' => 'Ativa',
            'inactive' => 'Inativa',
            'done' => 'Finalizada'
        ];

        return $rules[$state] ?? 'Desconhecido';
    }
}

if (!function_exists('formatDateBr')) {

    function formatDateBr($date, $format = 'd/m/Y H:i:s') {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : '-';
    }
}
