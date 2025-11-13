<x-layouts.auth-layout subtitle="{{ empty($subtitle) ? '' : $subtitle }}">

    <div class="main-card overflow-auto">

        <div class="flex justify-between items-center">
            <p class="title-2">Criar nova fila de espera</p>
            <a href="{{ route('home') }}" class="btn"><i class="fa-solid fa-arrow-left me-2"></i>Voltar</a>
        </div>

        <hr class="my-4">

        <div class="flex gap-4">

            <div class="w-1/2">

                <form action="{{ route('queue.create.submit') }}" method="POST" novalidate>

                    @csrf

                    {!! showServerError() !!}

                    <input type="hidden" name="hidden_hash_code" id="hidden_hash_code"  value="{{ old('hidden_hash_code') }}">

                    <div class="mb-4">
                        <label for="name" class="label">Nome da fila</label>
                        <input type="text" name="name" id="name" class="input w-full" placeholder="Nome da fila" value="{{ old('name') }}">
                        {!! showValidarionError('name', $errors) !!}
                    </div>

                    <div class="mb-4">
                        <label for="description" class="label">Descrição</label>
                        <input type="text" name="description" id="description" class="input w-full" placeholder="Descrição da fila" value="{{ old('description') }}">
                        {!! showValidarionError('description', $errors) !!}
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="w-1/2">
                            <label for="service" class="label">Serviço</label>
                            <input type="text" name="service" id="service" class="input w-full" placeholder="Serviço" value="{{ old('service') }}">
                            {!! showValidarionError('service', $errors) !!}
                        </div>

                        <div class="w-1/2">
                            <label for="desk" class="label">Balcão de atendimento</label>
                            <input type="text" name="desk" id="desk" class="input w-full" placeholder="Balcão de atendimento" value="{{ old('desk') }}">
                            {!! showValidarionError('desk', $errors) !!}
                        </div>
                    </div>

                    <div class="flex gap-4 mb-4">

                        <div class="w-full">
                            <label for="prefix" class="label">Prefixo</label>
                            <select name="prefix" id="prefix" class="input w-full">
                                <option value="-">Sem prefixo</option>
                                @php
                                    $prefixes = str_split('ABCDEFGHIJLMNOPQRSTUVWXYZ');
                                @endphp
                                @foreach ($prefixes as $prefix)
                                    <option value="{{ $prefix }}" {{ $prefix === 'A' || old('prefix') == $prefix ? 'selected' : '' }}>{{ $prefix }}</option>
                                @endforeach
                            </select>
                            {!! showValidarionError('prefix', $errors) !!}
                        </div>

                        <div class="w-full">
                            <label for="total_digits" class="label">Total de dígitos</label>
                            <select name="total_digits" id="total_digits" class="input w-full">
                                <option value="2" {{ old('total_digits') == '2' ? 'selected' : '' }}>00</option>
                                <option value="3" {{ old('total_digits') == '3' ? 'selected' : '' }}>000</option>
                                <option value="4" {{ old('total_digits') == '4' ? 'selected' : '' }}>0000</option>
                            </select>
                            {!! showValidarionError('total_digits', $errors) !!}
                        </div>

                        <div class="w-full">
                            <label for="status" class="label">Estado</label>
                            <select name="status" id="status" class="input w-full">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inativa</option>
                            </select>
                            {!! showValidarionError('status', $errors) !!}
                        </div>

                    </div>

                    <div class="mb-4">
                        <p class="label">Código de hash</p>
                        <div class="flex gap-2">
                            <p class="input bg-slate-100 w-full" id="hash_code">&nbsp;</p>
                            <button type="button" class="btn" id="btn_hash_code"><i class="fa-solid fa-rotate"></i></button>
                        </div>
                        {!! showValidarionError('hidden_hash_code', $errors) !!}
                    </div>

                    <div class="main-card flex !p-4 mb-4">

                        <div class="w-1/2">
                            <div class="mb-4">
                                <label class="label">Prefixo - Cor de fundo</label>
                                <input type="text" class="input text-zinc-900" name="color_1" id="color_1" value="{{ old('color_1', '#0d3561') }}">
                                {!! showValidarionError('color_1', $errors) !!}
                            </div>
                            <div>
                                <label class="label">Prefixo - Cor do texto</label>
                                <input type="text" class="input text-zinc-900" name="color_2" id="color_2" value="{{ old('color_2', '#ffffff') }}">
                                {!! showValidarionError('color_2', $errors) !!}
                            </div>
                        </div>

                        <div class="w-1/2">
                            <div class="mb-4">
                                <label class="label">Número - Cor de fundo</label>
                                <input type="text" class="input text-zinc-900" name="color_3" id="color_3" value="{{ old('color_3', '#adb4b9') }}">
                                {!! showValidarionError('color_3', $errors) !!}
                            </div>
                            <div>
                                <label class="label">Número - Cor do texto</label>
                                <input type="text" class="input text-zinc-900" name="color_4" id="color_4" value="{{ old('color_4', '#011020') }}">
                                {!! showValidarionError('color_4', $errors) !!}
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn"><i class="fa-solid fa-check me-2"></i>Criar nova fila</button>

                </form>

            </div>

            <div class="flex w-1/2 justify-center items-center">
                <div id="color_preview" class="flex main-card !bg-slate-200">
                    <p id="example_prefix" class="rounded-tl-2xl rounded-bl-2xl text-center text-9xl font-bold p-6" style="background-color: #0d3561; color: #ffffff;">A</p>
                    <p id="example_number" class="rounded-tr-2xl rounded-br-2xl text-center text-9xl font-bold p-6" style="background-color: #adb4b9; color: #011020;">01</p>
                </div>
            </div>

        </div>

    </div>

    <script>

        const fixedColors = [
            '#ff0000', '#660000', '#0000ff', '#000066', '#00ff00', '#006600',
            '#ffa800', '#aa6600', '#ffff00', '#666600', '#000000', '#ffffff'
        ];

        Coloris({ el: '#color_1', alpha: false, defaultColor: '#0d3561', swatches: fixedColors });
        Coloris({ el: '#color_2', alpha: false, defaultColor: '#ffffff', swatches: fixedColors });
        Coloris({ el: '#color_3', alpha: false, defaultColor: '#adb4b9', swatches: fixedColors });
        Coloris({ el: '#color_4', alpha: false, defaultColor: '#011020', swatches: fixedColors });

        const elPrefix = document.querySelector("#prefix");
        const elTotalDigits = document.querySelector("#total_digits");
        const elColor1 = document.querySelector("#color_1");
        const elColor2 = document.querySelector("#color_2");
        const elColor3 = document.querySelector("#color_3");
        const elColor4 = document.querySelector("#color_4");
        const elExamplePrefix = document.querySelector("#example_prefix");
        const elExampleNumber = document.querySelector("#example_number");

        function updateTicketPreview() {

            const ticketProperties = {
                hasPrefix: elPrefix.value !== '-',
                prefix: elPrefix.value,
                totalDigits: parseInt(elTotalDigits.value),
                prefixBackgroundColor: elColor1.value,
                prefixTextColor: elColor2.value,
                numberBackgroundColor: elColor3.value,
                numberTextColor: elColor4.value,
            }

            if(ticketProperties.hasPrefix) {
                elExamplePrefix.textContent = ticketProperties.prefix;
                elExamplePrefix.style.backgroundColor = ticketProperties.prefixBackgroundColor;
                elExamplePrefix.style.color = ticketProperties.prefixTextColor;
                elExamplePrefix.classList.remove('hidden');
                elExampleNumber.classList.remove('rounded-tl-2xl', 'rounded-bl-2xl');
            } else {
                elExamplePrefix.classList.add('hidden');
                elExampleNumber.classList.add('rounded-tl-2xl', 'rounded-bl-2xl');
            }

            elExampleNumber.textContent = String(1).padStart(ticketProperties.totalDigits, '0');
            elExampleNumber.style.backgroundColor = ticketProperties.numberBackgroundColor;
            elExampleNumber.style.color = ticketProperties.numberTextColor;
        }

        elPrefix.addEventListener('change', updateTicketPreview);
        elTotalDigits.addEventListener('change', updateTicketPreview);
        elColor1.addEventListener('change', updateTicketPreview);
        elColor2.addEventListener('change', updateTicketPreview);
        elColor3.addEventListener('change', updateTicketPreview);
        elColor4.addEventListener('change', updateTicketPreview);

        if('{{ old("color_1", "") }}') {
            updateTicketPreview();
        }

        function getHashCode() {
            fetch("{{ route('queue.generate.hash') }}")
                .then(res => res.json())
                .then(data => {
                    document.querySelector('#hash_code').textContent = data.hash;
                    document.querySelector('#hidden_hash_code').value = data.hash;
                }).catch(error => {
                    alert('Aconteceu um erro na criação do código de hash. Por favor tente novamente.');
                })
        }

        getHashCode();
        document.querySelector('#btn_hash_code').addEventListener('click', getHashCode);

    </script>


</x-layouts.auth-layout>
