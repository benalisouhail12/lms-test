<?php

namespace app\Modules\Authentication\Database\Factories;

use App\Modules\Authentication\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    // Define the model that this factory will create instances of
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'domain' => $this->faker->unique()->domainName,
            'keycloak_realm' => $this->faker->word,
            'is_active' => $this->faker->boolean,
        ];
    }
}
