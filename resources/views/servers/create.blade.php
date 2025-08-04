@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Create New Virtual Machine</h1>
            <a href="{{ route('projects.index') }}">
                <button class="btn btn-primary mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                    <i class="fas fa-back"></i> Back to Projects
                </button>
            </a>
        </div>
    </div>

    <!-- Create VM Form -->
    <div class="container py-4">
        <h1>Create New VM</h1>

        <form id="create-vm-form" method="POST" action="{{ route('servers.store') }}">
            @csrf
            <input type="hidden" name="flavor_id" id="selected_flavor_id" value="">
            <input type="hidden" name="image_id" id="selected_image_id" value="">
            <input type="hidden" name="network_id" id="selected_network_id" value="">

            {{-- VM Name --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            {{-- Flavor --}}
            <div class="mb-3">
                <label class="form-label h3 flavor">Flavor</label>
                <div class="row g-4">
                    @foreach($flavors as $f => $flavor)
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <input type="hidden" name="flavor_id" value="{{ $flavor['id'] }}">
                            <div class="card flavor-card">
                                <h5 class="card-header">{{ $flavor['name'] }}</h5>

                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>vCPU:</strong> {{ $flavor['vcpus'] }}<br>
                                        <strong>RAM:</strong> {{ floatval($flavor['ram']) / 1024 }} GB<br>
                                    </p>
                                    <button class="btn btn-primary select-flavor" type="button"
                                        data-flavor-id="{{ $flavor['id'] }}">
                                        Select
                                    </button>
                                </div>
                                <div class="selected-indicator" style="display: none;">
                                    <span class="badge bg-success">✓ Selected</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Image --}}
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <select name="image_select" id="image_select" class="form-select" required>
                        @foreach($images as $img)
                            <option value="{{ $img['id'] }}">{{ $img['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Networks --}}
                <!-- <div class="mb-3">
                                    <label class="form-label">Networks</label>
                                    <div class="row gy-2">
                                        {{-- NIC #1 (fixed IP) --}}
                                        <div class="col-md-6">
                                            <div class="card p-3">
                                                <h6>NIC 1 (fixed IP)</h6>
                                                <select name="network_select" id="network_select" class="form-select mb-2" required>
                                                    @foreach($networks as $n)
                                                        <option value="{{ $n['id'] }}">{{ $n['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="networks[0][fixed_ips][0][ip_address]" class="form-control"
                                                    placeholder="192.168.128.10" required>
                                            </div>
                                        </div>

                                        {{-- NIC #2 (automatic IP) --}}
                                        <div class="col-md-6">
                                            <div class="card p-3">
                                                <h6>NIC 2 (auto IP)</h6>
                                                <select name="networks[1][uuid]" class="form-select" required>
                                                    @foreach($networks as $n)
                                                        <option value="{{ $n['id'] }}">{{ $n['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                {{-- no fixed_ips field here so Nova will pick an IP from the subnet --}}
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                <!-- <div id="nics">
                    <div class="nic-row" data-index="0">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault1" checked>
                            <label class="form-check-label" for="radioDefault1">
                                Public Network
                             
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault2">
                            <label class="form-check-label" for="radioDefault2">
                                Private Network
                            </label>
                        </div>

                        {{-- fixed IP optional --}}

                        {{-- port security toggle optional --}}
                    </div>
                </div> -->
                {{-- Security Groups --}}


                {{-- Boot from Volume --}}
                <div class="mb-3">
                    <label class="form-label">Boot Volume</label>
                    <div class="input-group">
                        <input type="text" name="block_device[uuid]" class="form-control" placeholder="Image/Volume UUID">
                        <input type="number" name="block_device[volume_size]" class="form-control"
                            placeholder="Volume Size (GB)" min="1">
                        <span class="input-group-text">
                            <input type="checkbox" name="block_device[delete_on_termination]" checked>
                            Delete on Termination
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-server me-2"></i>Create VM
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            const flavorCards = document.querySelectorAll('.flavor-card');
            const selectButtons = document.querySelectorAll('.select-flavor');
            const selectedFlavorInput = document.getElementById('selected_flavor_id');
            const selectedImageInput = document.getElementById('selected_image_id');
            const imageSelect = document.getElementById('image_select');
            const networkSelect = document.getElementById('network_select');
            const selectedNetworkInput = document.getElementById('selected_network_id');
            //const createButton = document.getElementById('create-server');

            // Add click event to each select button
            selectButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const flavorId = this.dataset.flavorId;

                    // Remove selection from all cards
                    flavorCards.forEach(card => {
                        card.classList.remove('selected');
                        card.querySelector('.selected-indicator').style.display = 'none';
                        card.querySelector('.select-flavor').textContent = 'Select';
                    });

                    // Add selection to clicked card
                    const selectedCard = this.closest('.flavor-card');
                    selectedCard.classList.add('selected');
                    selectedCard.querySelector('.selected-indicator').style.display = 'block';
                    this.textContent = 'Selected';

                    // Update hidden input
                    selectedFlavorInput.value = flavorId;

                    // Enable submit button if both flavor and image are selected
                    // checkFormCompletion();
                    console.log(selectedFlavorInput.value);

                });
            });
            // Add change event to image select
            imageSelect.addEventListener('change', function () {
                const imageId = this.value;

                selectedImageInput.value = imageId;
                console.log(imageId);
                // Enable submit button if both flavor and image are selected
                // checkFormCompletion();
            });

            networkSelect.addEventListener('change', function () {
                const networkId = this.value;

                selectedNetworkInput.value = networkId;
                console.log(networkId);
                // Enable submit button if both flavor and image are selected
                // checkFormCompletion();
            });

            // function checkFormCompletion() {
            //     const flavorSelected = selectedFlavorInput.value !== '';
            //     const imageSelected = document.getElementById('selected_image_id').value !== '';

            //     createButton.disabled = !(flavorSelected && imageSelected);
            // }
        });
    </script>
@endpush