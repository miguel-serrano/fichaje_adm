<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\Delete;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Workplace\Domain\Exception\WorkplaceNotFoundException;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Backoffice\Workplace\Domain\Voter\WorkplaceVoter;

final class DeleteWorkplaceCommandHandler
{
    public function __construct(
        private readonly WorkplaceRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(DeleteWorkplaceCommand $command): void
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: WorkplaceVoter::delete(),
            userId: $command->activeUserId,
        );

        $workplaceId = WorkplaceId::create($command->workplaceId);
        $workplace = $this->repository->findById($workplaceId);

        if ($workplace === null) {
            throw WorkplaceNotFoundException::withId($workplaceId);
        }

        $this->repository->delete($workplace);
    }
}
