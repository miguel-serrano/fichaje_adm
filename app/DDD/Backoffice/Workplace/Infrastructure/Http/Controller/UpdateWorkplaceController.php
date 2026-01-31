<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Workplace\Application\Update\UpdateWorkplaceCommand;
use App\DDD\Backoffice\Workplace\Infrastructure\Http\Request\UpdateWorkplaceRequest;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class UpdateWorkplaceController extends BaseController
{
    public function __invoke(UpdateWorkplaceRequest $request, int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new UpdateWorkplaceCommand(
            activeUserId: $this->activeUserId(),
            workplaceId: $id,
            name: $request->string('name')->toString(),
            address: $request->filled('address') ? $request->string('address')->toString() : null,
            city: $request->filled('city') ? $request->string('city')->toString() : null,
            postalCode: $request->filled('postal_code') ? $request->string('postal_code')->toString() : null,
            latitude: $request->filled('latitude') ? $request->float('latitude') : null,
            longitude: $request->filled('longitude') ? $request->float('longitude') : null,
            radius: $request->filled('radius') ? $request->integer('radius') : null,
            isActive: $request->boolean('is_active', true),
        ));

        return redirect()
            ->route('backoffice.workplaces.show', $id)
            ->with('success', 'Centro de trabajo actualizado correctamente.');
    }
}
