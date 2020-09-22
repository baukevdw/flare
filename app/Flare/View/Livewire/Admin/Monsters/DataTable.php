<?php

namespace App\Flare\View\Livewire\Admin\Monsters;

use Livewire\Component;
use App\Flare\View\Livewire\Core\DataTable as CoreDataTable;
use App\Flare\Models\Monster;

class DataTable extends CoreDataTable
{

    public function mount() {
        $this->sortField = 'max_level';
    }
    
    public function render()
    {
        return view('components.livewire.admin.monsters.data-table', [
            'monsters' => Monster::dataTableSearch($this->search)
                                 ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                                 ->paginate($this->perPage),
        ]);
    }
}
