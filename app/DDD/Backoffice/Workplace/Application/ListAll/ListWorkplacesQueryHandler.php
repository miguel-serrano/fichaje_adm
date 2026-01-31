<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\ListAll;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\Voter\WorkplaceVoter;
use App\DDD\Backoffice\Workplace\Domain\Workplace;

final class ListWorkplacesQueryHandler
{
    public function __construct(
        private readonly WorkplaceRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(ListWorkplacesQuery $query): ListWorkplacesResponse
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: WorkplaceVoter::view(),
            userId: $query->activeUserId,
        );

        $workplaces = $query->onlyActive
            ? $this->repository->findActive()
            : $this->repository->findAll();

        $items = array_map(
            fn (Workplace $workplace) => WorkplaceItem::fromWorkplace($workplace),
            $workplaces,
        );

        return new ListWorkplacesResponse($items);
    }
}
