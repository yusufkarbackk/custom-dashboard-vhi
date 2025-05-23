@props(['groups', 'field'])

<div {{ $attributes->merge(['class' => 'mb-5']) }}>
    {{-- Hidden field --}}
    <input type="hidden" name="{{ $field }}" id="{{ $field }}" required>

    {{-- Nav tabs --}}
    <ul class="nav nav-tabs" role="tablist">
        @foreach(array_keys($groups) as $i => $size)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($i === 0) active @endif" id="tab-{{ Str::slug($size) }}" data-bs-toggle="tab"
                    data-bs-target="#pane-{{ Str::slug($size) }}" type="button" role="tab">
                    {{ $size }}
                </button>
            </li>
        @endforeach
    </ul>

    {{-- Tab panes --}}
    <div class="tab-content pt-3">
        @foreach($groups as $i => $flavors)
            @php $size = array_keys($groups)[$i] @endphp
            <div class="tab-pane fade @if($i === 0) show active @endif" id="pane-{{ Str::slug($size) }}" role="tabpanel">
                <div class="row g-3">
                    @foreach($flavors as $flavor)
                        <div class="col-md-4">
                            <div class="card flavor-card" data-flavor-id="{{ $flavor['id'] }}" style="cursor:pointer">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $flavor['name'] }}</h5>
                                    <p class="card-text mb-1">
                                        <strong>vCPU:</strong> {{ $flavor['vcpus'] }}
                                    </p>
                                    <p class="card-text">
                                        <strong>RAM:</strong> {{ intdiv($flavor['ram'], 1024) }} GB
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-text mt-2">Click a card to select your flavor.</div>
</div>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('.flavor-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.flavor-card.active')
                        .forEach(c => c.classList.remove('active', 'border-primary'));
                    card.classList.add('active', 'border-primary');
                    document.getElementById('{{ $field }}').value =
                        card.dataset.flavorId;
                });
            });
        </script>
    @endpush
@endonce