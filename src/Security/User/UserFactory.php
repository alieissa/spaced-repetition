<?php

namespace App\Security\User;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<UserEntity>
 *
 * @method static UserEntity|Proxy createOne(array $attributes = [])
 * @method static UserEntity[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static UserEntity[]|Proxy[] createSequence(array|callable $sequence)
 * @method static UserEntity|Proxy find(object|array|mixed $criteria)
 * @method static UserEntity|Proxy findOrCreate(array $attributes)
 * @method static UserEntity|Proxy first(string $sortedField = 'id')
 * @method static UserEntity|Proxy last(string $sortedField = 'id')
 * @method static UserEntity|Proxy random(array $attributes = [])
 * @method static UserEntity|Proxy randomOrCreate(array $attributes = [])
 * @method static UserEntity[]|Proxy[] all()
 * @method static UserEntity[]|Proxy[] findBy(array $attributes)
 * @method static UserEntity[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static UserEntity[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method UserEntity|Proxy create(array|callable $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
        $this->passwordHasher = $passwordHasher;
    }

    protected static function getClass(): string
    {
        return UserEntity::class;
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'email'         => self::faker()->email(),
            'plainPassword' => 'tada',
            "username"      => "fake",
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            ->afterInstantiate(function (UserEntity $user) {
                if ($user->getPlainPassword()) {
                    $user->setPassword(
                        $this->passwordHasher->hashPassword(
                            $user, $user->getPlainPassword()
                        )
                    );
                }
            });
    }
}
