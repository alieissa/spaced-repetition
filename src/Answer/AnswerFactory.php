<?php

namespace App\Answer;

use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<AnswerEntity>
 *
 * @method static AnswerEntity|Proxy createOne(array $attributes = [])
 * @method static AnswerEntity[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static AnswerEntity[]|Proxy[] createSequence(array|callable $sequence)
 * @method static AnswerEntity|Proxy find(object|array|mixed $criteria)
 * @method static AnswerEntity|Proxy findOrCreate(array $attributes)
 * @method static AnswerEntity|Proxy first(string $sortedField = 'id')
 * @method static AnswerEntity|Proxy last(string $sortedField = 'id')
 * @method static AnswerEntity|Proxy random(array $attributes = [])
 * @method static AnswerEntity|Proxy randomOrCreate(array $attributes = [])
 * @method static AnswerEntity[]|Proxy[] all()
 * @method static AnswerEntity[]|Proxy[] findBy(array $attributes)
 * @method static AnswerEntity[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static AnswerEntity[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static AnswerRepository|RepositoryProxy repository()
 * @method AnswerEntity|Proxy create(array|callable $attributes = [])
 */
final class AnswerFactory extends ModelFactory {
    public function __construct() {
        parent::__construct();
    }

    protected static function getClass(): string {
        return AnswerEntity::class;
    }

    protected function getDefaults(): array {
        return ['content' => self::faker()->text(),];
    }

    protected function initialize(): self {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this// ->afterInstantiate(function(AnswerEntity $answer): void {})
            ;
    }
}
