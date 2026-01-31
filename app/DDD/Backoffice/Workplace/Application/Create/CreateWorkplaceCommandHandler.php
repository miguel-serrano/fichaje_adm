<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\Create;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\Voter\WorkplaceVoter;
use App\DDD\Backoffice\Workplace\Domain\Workplace;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceAddress;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceCity;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceLatitude;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceLongitude;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceName;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplacePostalCode;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceRadius;

final class CreateWorkplaceCommandHandler
{
    public function __construct(
        private readonly WorkplaceRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(CreateWorkplaceCommand $command): void
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: WorkplaceVoter::create(),
            userId: $command->activeUserId,
        );

        $workplace = Workplace::create(
            id: $this->repository->nextId(),
            name: WorkplaceName::create($command->name),
            address: $command->address !== null
                ? WorkplaceAddress::create($command->address)
                : null,
            city: $command->city !== null
                ? WorkplaceCity::create($command->city)
                : null,
            postalCode: $command->postalCode !== null
                ? WorkplacePostalCode::create($command->postalCode)
                : null,
            latitude: $command->latitude !== null
                ? WorkplaceLatitude::create($command->latitude)
                : null,
            longitude: $command->longitude !== null
                ? WorkplaceLongitude::create($command->longitude)
                : null,
            radius: $command->radius !== null
                ? WorkplaceRadius::create($command->radius)
                : null,
        );

        $this->repository->save($workplace);
    }
}
