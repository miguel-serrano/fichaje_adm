<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\Find;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Workplace\Domain\Exception\WorkplaceNotFoundException;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Backoffice\Workplace\Domain\Voter\WorkplaceVoter;

final class FindWorkplaceQueryHandler
{
    public function __construct(
        private readonly WorkplaceRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(FindWorkplaceQuery $query): FindWorkplaceResponse
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: WorkplaceVoter::view(),
            userId: $query->activeUserId,
        );

        $workplace = $this->repository->findById(WorkplaceId::create($query->workplaceId));

        if ($workplace === null) {
            throw WorkplaceNotFoundException::withId(WorkplaceId::create($query->workplaceId));
        }

        return FindWorkplaceResponse::fromWorkplace($workplace);
    }
}
