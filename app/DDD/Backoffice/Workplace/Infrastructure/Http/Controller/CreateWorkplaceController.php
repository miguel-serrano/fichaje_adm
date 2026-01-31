<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Http\Controller;

use App\DDD\Backoffice\Workplace\Application\Create\CreateWorkplaceCommand;
use App\DDD\Backoffice\Workplace\Infrastructure\Http\Request\CreateWorkplaceRequest;
use App\DDD\Shared\Infrastructure\Http\Controller\BaseController;
use Illuminate\Http\RedirectResponse;

final class CreateWorkplaceController extends BaseController
{
    public function __invoke(CreateWorkplaceRequest $request): RedirectResponse
    {
        $this->commandBus->dispatch(new CreateWorkplaceCommand(
            activeUserId: $this->activeUserId(),
            name: $request->string('name')->toString(),
            address: $request->filled('address') ? $request->string('address')->toString() : null,
            city: $request->filled('city') ? $request->string('city')->toString() : null,
            postalCode: $request->filled('postal_code') ? $request->string('postal_code')->toString() : null,
            latitude: $request->filled('latitude') ? $request->float('latitude') : null,
            longitude: $request->filled('longitude') ? $request->float('longitude') : null,
            radius: $request->filled('radius') ? $request->integer('radius') : null,
        ));

        return redirect()
            ->route('backoffice.workplaces.index')
            ->with('success', 'Centro de trabajo creado correctamente.');
    }
}
