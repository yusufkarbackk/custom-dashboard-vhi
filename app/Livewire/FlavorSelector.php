<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class FlavorSelector extends Component
{

    public $flavors = [];
    public $selectedFlavor;

    public function mount($flavors)
    {
        $this->flavors = $flavors;
        $this->selectedFlavor = session('selected_flavor', null);
    }

    public function selectFlavor($flavorId)
    {
        $this->selectedFlavor = $flavorId;
        Session::put('selected_flavor', $flavorId);
    }

    public function render()
    {
        return view('livewire.flavor-selector');
    }
}
    