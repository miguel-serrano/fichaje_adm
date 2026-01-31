<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\Update;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Workplace\Domain\Exception\WorkplaceNotFoundException;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Backoffice\Workplace\Domain\Voter\WorkplaceVoter;
use App\DDD\Backoffice\Workplace\Domain\Workplace;

final class UpdateWorkplaceCommandHandler
{
    public function __construct(
        private readonly WorkplaceRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(UpdateWorkplaceCommand $command): void
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: WorkplaceVoter::edit(),
            userId: $command->activeUserId,
        );

        $workplaceId = WorkplaceId::create($command->workplaceId);
        $workplace = $this->repository->findById($workplaceId);

        if ($workplace === null) {
            throw WorkplaceNotFoundException::withId($workplaceId);
        }

        $updated = Workplace::fromPrimitives([
            'id' => $command->workplaceId,
            'name' => $command->name,
            'address' => $command->address,
            'city' => $command->city,
            'postal_code' => $command->postalCode,
            'latitude' => $command->latitude,
            'longitude' => $command->longitude,
            'radius' => $command->radius,
            'is_active' => $command->isActive,
        ]);

        $this->repository->save($updated);
    }
}
