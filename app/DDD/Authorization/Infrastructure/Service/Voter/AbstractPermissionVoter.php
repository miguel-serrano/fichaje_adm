<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Infrastructure\Service\Voter;

use App\DDD\Authorization\Domain\Interface\VoteResult;
use App\DDD\Authorization\Infrastructure\Service\UserPermissionsCheckerInterface;

abstract class AbstractPermissionVoter extends AbstractVoter
{
    private const PREFIX = 'permission_';
    private const ACTION_VIEW = 'view';
    private const ACTION_CREATE = 'create';
    private const ACTION_EDIT = 'edit';
    private const ACTION_DELETE = 'delete';

    public function __construct(
        protected readonly UserPermissionsCheckerInterface $permissionsChecker,
    ) {}

    abstract protected function voterName(): string;

    public static function view(): string
    {
        return self::PREFIX . static::getStaticVoterName() . '_' . self::ACTION_VIEW;
    }

    public static function create(): string
    {
        return self::PREFIX . static::getStaticVoterName() . '_' . self::ACTION_CREATE;
    }

    public static function edit(): string
    {
        return self::PREFIX . static::getStaticVoterName() . '_' . self::ACTION_EDIT;
    }

    public static function delete(): string
    {
        return self::PREFIX . static::getStaticVoterName() . '_' . self::ACTION_DELETE;
    }

    public function supportedAttributes(): array
    {
        $name = $this->voterName();

        return [
            self::PREFIX . $name . '_' . self::ACTION_VIEW,
            self::PREFIX . $name . '_' . self::ACTION_CREATE,
            self::PREFIX . $name . '_' . self::ACTION_EDIT,
            self::PREFIX . $name . '_' . self::ACTION_DELETE,
        ];
    }

    protected function voteOnAttribute(int $userId, string $attribute, mixed $subject): VoteResult
    {
        $action = $this->extractAction($attribute);

        $isAuthorized = match ($action) {
            self::ACTION_VIEW => $this->canView($userId, $subject),
            self::ACTION_CREATE => $this->canCreate($userId, $subject),
            self::ACTION_EDIT => $this->canEdit($userId, $subject),
            self::ACTION_DELETE => $this->canDelete($userId, $subject),
            default => false,
        };

        return $isAuthorized ? $this->grantAccess() : $this->denyAccess();
    }

    protected function canView(int $userId, mixed $subject): bool
    {
        return $this->permissionsChecker->hasPermission($userId, $this->voterName(), self::ACTION_VIEW);
    }

    protected function canCreate(int $userId, mixed $subject): bool
    {
        return $this->permissionsChecker->hasPermission($userId, $this->voterName(), self::ACTION_CREATE);
    }

    protected function canEdit(int $userId, mixed $subject): bool
    {
        return $this->permissionsChecker->hasPermission($userId, $this->voterName(), self::ACTION_EDIT);
    }

    protected function canDelete(int $userId, mixed $subject): bool
    {
        return $this->permissionsChecker->hasPermission($userId, $this->voterName(), self::ACTION_DELETE);
    }

    private function extractAction(string $attribute): string
    {
        $parts = explode('_', $attribute);
        return end($parts);
    }

    /**
     * Override en cada voter concreto para que los métodos estáticos funcionen
     */
    protected static function getStaticVoterName(): string
    {
        throw new \LogicException('Override getStaticVoterName() in child class to use static methods');
    }
}
