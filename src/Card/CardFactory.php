<?php

namespace App\Card;

use DateTimeImmutable;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<CardEntity>
 *
 * @method static CardEntity|Proxy createOne(array $attributes = [])
 * @method static CardEntity[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CardEntity[]|Proxy[] createSequence(array|callable $sequence)
 * @method static CardEntity|Proxy find(object|array|mixed $criteria)
 * @method static CardEntity|Proxy findOrCreate(array $attributes)
 * @method static CardEntity|Proxy first(string $sortedField = 'id')
 * @method static CardEntity|Proxy last(string $sortedField = 'id')
 * @method static CardEntity|Proxy random(array $attributes = [])
 * @method static CardEntity|Proxy randomOrCreate(array $attributes = [])
 * @method static CardEntity[]|Proxy[] all()
 * @method static CardEntity[]|Proxy[] findBy(array $attributes)
 * @method static CardEntity[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CardEntity[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static CardRepository|RepositoryProxy repository()
 * @method CardEntity|Proxy create(array|callable $attributes = [])
 */
final class CardFactory extends ModelFactory {
    public function __construct() {
        parent::__construct();
    }

    protected static function getClass(): string {
        return CardEntity::class;
    }

    protected function getDefaults(): array {
        return [
            'question'           => self::faker()->text(),
            'easiness'           => self::faker()->randomFloat(1, 1.3, 2.5),
            'quality'            => self::faker()->numberBetween(0, 5),
            // TODO Use algorithm to calculate interval
            'interval'           => self::faker()->randomNumber(),
            'repetitions'        => self::faker()->randomNumber(),
            // TODO Use algorithm to calculate next_practice_date
            'next_practice_date' => self::faker()->dateTime(),
            'createdAt'          => DateTimeImmutable::createFromMutable(
                self::faker()->dateTime()
            ),
        ];
    }

    protected function initialize(): self {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(Card $card): void {})
            ;
    }
}
