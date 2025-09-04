<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Facades\StatusCheckService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('system.index')]
final class System extends Component
{
    use WithBreadcrumbs;

    public function render(): View
    {
        $this->withBreadcrumbs(new BreadcrumbItemData(__('system.index')));

        return view('livewire.admin.system', [
            'status' => StatusCheckService::check(),
            'valid' => StatusCheckService::isValid(),
            'errors' => StatusCheckService::hasErrors(),
        ]);
    }
}
