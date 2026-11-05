<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Accounting\Erp\Models\Menu;

class MenuFactory extends Factory
{
    #[\Override]
    protected $model = Menu::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'url' => $this->faker->url,
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
