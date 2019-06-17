<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Faker\Generator as Faker;

class UsersTableSeeder extends Seeder
{
    private const MIN = 3;
    private const MAX = 10;
    private const STARTING_PERCENTAGE = 120;
    private const DECAY = 25;

    /**
     * @var Faker
     */
    private $faker;

    /**
     * UsersTableSeeder constructor.
     * @param Faker $faker
     */
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $user = factory(User::class)->create(['name' => 'Joe Dirt']);

        $this->createChildren($user);
    }

    /**
     * @param User $user
     * @return void
     */
    private function createChildren(User $user): void
    {
        ini_set('xdebug.max_nesting_level', 20000);
        try {
            $this->recursiveChildren($user, self::STARTING_PERCENTAGE);
        } catch (Throwable $e) {
            Log::info(sprintf('Probably hit the function nesting limit: %s', $e->getMessage()));
        }
    }

    /**
     * @param User $user
     * @param int $percentage
     * @return void
     */
    private function recursiveChildren(User $user, int $percentage): void
    {
        $roll = $this->faker->numberBetween(1, 100);
        Log::info(sprintf('roll: %d, percentage: %d', $roll, $percentage));
        if ($roll > $percentage) {
            return;
        }

        $count = $this->faker->numberBetween(self::MIN, self::MAX);
        /**
         * @var Collection|User[] $children
         */
        $children = factory(User::class, $count)->create();
        if ($children->isEmpty()) {
            return;
        }

        $user->reports()->saveMany($children);
        $children->each(function (User $child) use ($percentage): void {
            $this->recursiveChildren($child, $percentage - self::DECAY);
        });
    }
}
