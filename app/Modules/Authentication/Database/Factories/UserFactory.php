<?php
namespace app\Modules\Authentication\Database\Factories;

use App\Modules\Authentication\Models\Tenant;
use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'keycloak_id' => $this->faker->uuid,
            'tenant_id' => TenantFactory::new(),
            'email_verified_at' => now(),
            'is_active' => $this->faker->boolean(90),
            'last_login_at' => $this->faker->dateTimeThisYear(),
            'remember_token' => \Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function asAdmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
            ];
        });
    }



    public function asStudent()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'student',
            ];
        });
    }

    /**
     * Assign role after user creation.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function assignRoleAfterCreation()
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('instructor');  // Or any role you need to assign
        });
    }
}
