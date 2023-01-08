<?php

namespace App\Deck;

use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<DeckEntity>
 *
 * @method static DeckEntity|Proxy createOne(array $attributes = [])
 * @method static DeckEntity[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static DeckEntity[]|Proxy[] createSequence(array|callable $sequence)
 * @method static DeckEntity|Proxy find(object|array|mixed $criteria)
 * @method static DeckEntity|Proxy findOrCreate(array $attributes)
 * @method static DeckEntity|Proxy first(string $sortedField = 'id')
 * @method static DeckEntity|Proxy last(string $sortedField = 'id')
 * @method static DeckEntity|Proxy random(array $attributes = [])
 * @method static DeckEntity|Proxy randomOrCreate(array $attributes = [])
 * @method static DeckEntity[]|Proxy[] all()
 * @method static DeckEntity[]|Proxy[] findBy(array $attributes)
 * @method static DeckEntity[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static DeckEntity[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static DeckRepository|RepositoryProxy repository()
 * @method DeckEntity|Proxy create(array|callable $attributes = [])
 */
final class DeckFactory extends ModelFactory {
    public function __construct() {
        parent::__construct();
    }

    protected static function getClass(): string {
        return DeckEntity::class;
    }

    protected function getDefaults(): array {
        return ['name'      => self::faker()->text(),
                'createdAt' => DateTimeImmutable::createFromMutable(
                    self::faker()->dateTime()
                ),];
    }

    protected function initialize(): self {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(Deck $deck): void {})
            ;
    }
}
