<div class="row g-4">
    @foreach($flavors as $flavor)
    <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 20px;">
        <strong>{{ $flavor['name'] ?? $flavor['id'] }}</strong>
        <div><b>vCPU:</b> {{ $flavor['vcpus'] }}</div>
        <div><b>RAM:</b> {{ $flavor['ram'] }} GB</div>

        @if($selectedFlavor === ($flavor['id']))
        <button class="btn btn-primary" disabled>Selected</button>
        <span style="color: green; font-weight: bold;">✔ Selected</span>
        @else
        <button type="button" wire:click="selectFlavor('{{ $flavor['id'] }}')" class="btn btn-primary">
            Select
        </button>
        @endif
    </div>
    @endforeach
</div>  