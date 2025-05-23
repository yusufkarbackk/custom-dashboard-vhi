<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FlavorPicker extends Component
{

    /**
     * An associative array: [ 'Small' => [flavor1, flavor2], 'Medium' => […], … ]
     *
     * @var array<string, array>
     */
    public $groups;

    /**
     * The name of the form field to bind (e.g. 'flavor_id')
     *
     * @var string
     */
    public $field;

    /**
     * Create a new component instance.
     *
     * @param  array  $groups
     * @param  string $field
     */


    /**
     * Create a new component instance.
     */
    public function __construct(array $groups, string $field = 'flavor_id')
    {
        $this->field = $field;
        $this->groups = $groups;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.flavor-picker');
    }
}
