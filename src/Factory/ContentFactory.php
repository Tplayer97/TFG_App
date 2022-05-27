<?php

namespace App\Factory;

use App\Entity\Content;
use App\Repository\ContentRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Content>
 *
 * @method static Content|Proxy createOne(array $attributes = [])
 * @method static Content[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Content|Proxy find(object|array|mixed $criteria)
 * @method static Content|Proxy findOrCreate(array $attributes)
 * @method static Content|Proxy first(string $sortedField = 'id')
 * @method static Content|Proxy last(string $sortedField = 'id')
 * @method static Content|Proxy random(array $attributes = [])
 * @method static Content|Proxy randomOrCreate(array $attributes = [])
 * @method static Content[]|Proxy[] all()
 * @method static Content[]|Proxy[] findBy(array $attributes)
 * @method static Content[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Content[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ContentRepository|RepositoryProxy repository()
 * @method Content|Proxy create(array|callable $attributes = [])
 */
final class ContentFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'Title' => self::faker()->text(),
            'Score' => self::faker()->randomNumber(),

        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Content $content): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Content::class;
    }
}
