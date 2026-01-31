<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Workplace\Application\Delete\DeleteWorkplaceCommand;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class DeleteWorkplaceController extends BaseController
{
    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteWorkplaceCommand(
            activeUserId: $this->activeUserId(),
            workplaceId: $id,
        ));

        return redirect()
            ->route('backoffice.workplaces.index')
            ->with('success', 'Centro de trabajo eliminado correctamente.');
    }
}
